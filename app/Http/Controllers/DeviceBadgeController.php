<?php

namespace App\Http\Controllers;

use App\Models\TrustedDevice;
use App\Services\DeviceBadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceBadgeController extends Controller
{
    /**
     * Enrôler l'appareil actuel comme badge de pointage.
     */
    public function enroll(Request $request, DeviceBadgeService $badgeService)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour enrôler cet appareil.');
        }

        $result = $badgeService->enrollDevice($user, $request);

        $deviceName = $result['device']->device_name;

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Cet appareil ({$deviceName}) a été configuré avec succès comme votre badge de présence !",
                'device'  => $result['device'],
            ])->withCookie($result['cookie']);
        }

        return back()
            ->withCookie($result['cookie'])
            ->with('success', "🎉 Votre téléphone ({$deviceName}) est maintenant configuré comme votre badge de présence ! Vous pourrez scanner les QR codes d'activités directement sans vous reconnecter.");
    }

    /**
     * Révoquer un appareil de confiance.
     */
    public function revoke(Request $request, TrustedDevice $device, DeviceBadgeService $badgeService)
    {
        $user = Auth::user();

        if (!$user || $device->user_id !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        $deviceName = $device->device_name;
        $device->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "L'appareil \"{$deviceName}\" a été révoqué avec succès.",
            ]);
        }

        return back()->with('info', "L'appareil \"{$deviceName}\" ne pourra plus être utilisé comme badge automatique.");
    }
}
