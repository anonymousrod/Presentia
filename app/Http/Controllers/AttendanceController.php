<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use App\Services\DeviceBadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Valide la présence via un QR Code (URL signée).
     * Supporte :
     * 1. Session active classique (Auth::user())
     * 2. Appareil-Badge (Scan direct via appareil photo natif sans login manuel)
     * 3. AJAX POST (Scanner interne dans l'application)
     */
    public function validate(Request $request, DeviceBadgeService $badgeService)
    {
        // 1. Identification de l'utilisateur (Session ou Appareil-Badge)
        $user = Auth::user();

        if (!$user) {
            $user = $badgeService->resolveUserFromCookie($request);
            if ($user) {
                Auth::login($user);
            }
        }

        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Authentification requise pour valider votre présence.',
                ], 401);
            }

            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('login', ['redirect' => $request->fullUrl()])
                ->with('info', '👋 Bonjour ! Veuillez vous connecter pour enregistrer votre présence. Vous pourrez ensuite faire de ce téléphone votre badge automatique en 1 clic !');
        }

        // 2. Validation personnalisée de la signature pour supporter ngrok / localhost
        $isValid = $request->hasValidSignature();

        if (!$isValid) {
            $signature = $request->query('signature');
            $query = $request->query();
            unset($query['signature']);

            // F-19 : Vérifier l'expiration AVANT de recalculer la signature.
            $expires = (int) ($query['expires'] ?? 0);
            if ($expires > 0 && $expires < now()->timestamp) {
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Ce QR code a expiré.'], 403);
                }
                return redirect()->route('activities.index')->with('warning', 'Ce QR code a expiré.');
            }

            $queryString = http_build_query($query);
            $urlPath = $request->path();

            // F-19 : Hôtes localhost retirés en production pour éviter la falsification.
            $possibleHosts = [config('app.url')];
            if (app()->environment('local', 'testing')) {
                $possibleHosts = array_merge($possibleHosts, [
                    'http://127.0.0.1:8000',
                    'http://localhost:8000',
                ]);
            }

            $matched = false;
            foreach ($possibleHosts as $host) {
                $host = rtrim($host, '/');
                $testUrl = $host . '/' . $urlPath . ($queryString ? '?' . $queryString : '');
                if (hash_equals(hash_hmac('sha256', $testUrl, config('app.key')), (string) $signature)) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Lien expiré ou signature invalide.'], 403);
                }
                return redirect()->route('activities.index')->with('warning', 'Lien de présence expiré ou invalide.');
            }
        }

        $activityIdHash = $request->query('activity');
        $activityId = decode_id($activityIdHash);
        $version = $request->query('v');

        $activity = Activity::find($activityId);

        if (!$activity) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Activité introuvable.'], 404);
            }
            return redirect()->route('activities.index')->with('info', 'L\'activité demandée n\'est plus disponible.');
        }

        // Vérifier si l'utilisateur connecté est inscrit à l'activité (si inscription requise)
        $registration = \App\Models\Registration::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        $isRegistered = $registration && $registration->status !== 'ABSENT_JUSTIFIED';

        if ($activity->is_registration_required && !$isRegistered) {
            $errorMessage = "Vous ne pouvez pas valider votre présence sans être préalablement inscrit à cette activité.";
            if ($activity->start_time->lte(now())) {
                $errorMessage .= " L'inscription est désormais clôturée. Veuillez contacter votre responsable de groupe ou un membre du bureau.";
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], 403);
            }
            return redirect()->route('activities.show', $activity)->with('warning', $errorMessage);
        }

        // Vérification de la version du QR Code
        if ($version != $activity->qr_version) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ce QR Code a été révoqué ou mis à jour.'
                ], 403);
            }
            return redirect()->route('activities.show', $activity)->with('warning', 'Ce QR Code a été révoqué ou mis à jour.');
        }

        // Vérifier si la présence est déjà enregistrée
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if ($existingAttendance) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous avez déjà validé votre présence pour cette activité.',
                    'already_scanned' => true,
                    'data' => [
                        'activity' => $activity->title,
                        'status' => $existingAttendance->status->value,
                        'scanned_at' => $existingAttendance->scanned_at->format('H:i:s'),
                    ]
                ], 200);
            }
            return redirect()->route('attendance.success', $activity)
                ->with('info', 'Votre présence était déjà enregistrée à ' . $existingAttendance->scanned_at->format('H\hi') . '.');
        }

        // Calcul du statut (PRESENT ou LATE)
        // Seuil de retard : 15 minutes après l'heure de début
        $startTime = $activity->start_time;
        $now = now();
        $lateThreshold = $startTime->copy()->addMinutes(15);

        $status = $now->gt($lateThreshold) ? AttendanceStatus::LATE : AttendanceStatus::PRESENT;

        // Création de la présence
        $attendance = Attendance::create([
            'user_id'     => $user->id,
            'activity_id' => $activity->id,
            'status'      => $status,
            'scan_source' => 'qr_code',
            'scanned_at'  => $now,
            'ip_address'  => $request->ip(),
        ]);

        $wasRecentlyCreated = $attendance->wasRecentlyCreated;

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Présence validée !',
                'already_scanned' => false,
                'data' => [
                    'activity' => $activity->title,
                    'status' => $status->value,
                    'scanned_at' => $now->format('H:i:s'),
                ]
            ]);
        }

        // Pour un scan via navigateur (GET), redirection fluide vers la page de succès
        return redirect()->route('attendance.success', $activity)
            ->with('success', $wasRecentlyCreated ? "Votre présence a été validée avec succès !" : 'Votre présence était déjà enregistrée.');
    }
}
