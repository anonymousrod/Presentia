<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class AttendanceScanController extends Controller
{
    /**
     * Affiche la page du scanner QR Code (caméra).
     * Accessible par tous les utilisateurs authentifiés.
     */
    public function scanner()
    {
        return view('attendance.scanner');
    }

    /**
     * Affiche la page de confirmation après un scan réussi.
     */
    public function success(Activity $activity)
    {
        return view('attendance.success', compact('activity'));
    }

    /**
     * Méthode dépréciée ou conservée pour compatibilité si l'URL QR pointe encore ici.
     * Redirige vers validate si signature présente.
     */
    public function scan(Request $request)
    {
        if ($request->hasValidSignature()) {
            return app(AttendanceController::class)->validate($request);
        }

        return redirect()->route('attendance.scanner');
    }
}
