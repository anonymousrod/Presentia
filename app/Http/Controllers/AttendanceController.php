<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Valide la présence via un QR Code (URL signée).
     * Supporte AJAX POST (scanner interne) et GET (scanner natif).
     */
    public function validate(Request $request)
    {
        // Validation personnalisée de la signature pour supporter ngrok / localhost
        $isValid = $request->hasValidSignature();
        
        if (!$isValid) {
            $signature = $request->query('signature');
            $query = $request->query();
            unset($query['signature']);
            
            $queryString = http_build_query($query);
            $urlPath = $request->path();
            
            $possibleHosts = [
                'http://127.0.0.1:8000',
                'http://localhost:8000',
                config('app.url')
            ];
            
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
                    return response()->json(['status' => 'error', 'message' => 'Lien expiré ou signature invalide (Problème de domaine ngrok/localhost).'], 403);
                }
                abort(403, 'Lien expiré ou signature invalide.');
            }
        }

        $activityId = $request->query('activity');
        $version = $request->query('v');

        $activity = Activity::findOrFail($activityId);

        // Vérifier si l'utilisateur connecté est inscrit à l'activité
        $registration = \App\Models\Registration::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->first();

        $isRegistered = $registration && $registration->status !== 'ABSENT_JUSTIFIED';

        if ($activity->is_registration_required && !$isRegistered) {
            $errorMessage = "Vous ne pouvez pas valider votre présence sans être inscrit à cette activité.";
            if ($activity->start_time->lte(now())) {
                $errorMessage .= " Vous ne pouvez plus vous inscrire à cette activité. Veuillez contacter votre responsable de groupe ou le Président de la jeunesse afin qu'il puisse marquer votre présence.";
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage
                ], 403);
            }
            abort(403, $errorMessage);
        }

        // Vérification de la version du QR Code
        if ($version != $activity->qr_version) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ce QR Code a été révoqué ou mis à jour.'
                ], 403);
            }
            abort(403, 'Ce QR Code a été révoqué ou mis à jour.');
        }

        // Vérifier si la présence est déjà enregistrée
        $existingAttendance = Attendance::where('user_id', Auth::id())
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
                ], 200); // On renvoie 200 pour que le JS gère l'affichage "déjà fait" proprement
            }
            return redirect()->route('attendance.success', $activity->id)
                ->with('info', 'Votre présence était déjà enregistrée.');
        }

        // Calcul du statut (PRESENT ou LATE)
        // Seuil de retard : 15 minutes après l'heure de début
        $startTime = $activity->start_time;
        $now = now();
        $lateThreshold = $startTime->copy()->addMinutes(15);

        $status = $now->gt($lateThreshold) ? AttendanceStatus::LATE : AttendanceStatus::PRESENT;

        // Création de la présence
        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'activity_id' => $activity->id,
            'status' => $status,
            'scan_source' => 'qr_code',
            'scanned_at' => $now,
            'ip_address' => $request->ip(),
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

        // Pour un scan via navigateur (GET), on redirige vers la page de succès
        return redirect()->route('attendance.success', $activity->id)
            ->with('success', $wasRecentlyCreated ? 'Votre présence a été validée.' : 'Votre présence était déjà enregistrée.');
    }
}
