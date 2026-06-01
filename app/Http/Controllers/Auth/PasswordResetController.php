<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function __construct(
        protected PasswordResetService $resetService
    ) {
    }

    /**
     * Affiche le formulaire de demande de réinitialisation.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Traite la demande de réinitialisation.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->identifier);

        // Normalisation email: les emails sont insensibles à la casse
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $identifier = mb_strtolower($identifier);
        }

        $result = $this->resetService->sendResetRequest($identifier);


        if ($result['success']) {
            return back()->with('status', $result['message']);
        }

        return back()->withErrors(['identifier' => $result['message']]);
    }
}
