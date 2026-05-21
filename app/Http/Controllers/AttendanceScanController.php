<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class AttendanceScanController extends Controller
{
    /**
     * Valide le scan du QR Code et affiche la confirmation de présence.
     */
    public function scan(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Le QR Code a expiré ou la signature est invalide.');
        }

        $activityId = $request->query('activity');
        $version = $request->query('v');

        $activity = Activity::findOrFail($activityId);

        if ($version != $activity->qr_version) {
            abort(403, 'Ce QR Code a été révoqué ou mis à jour.');
        }

        // Nous passons l'activité et le statut de validation à la vue
        return view('attendance.scan', compact('activity'));
    }
}
