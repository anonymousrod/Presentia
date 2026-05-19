<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    /**
     * Affiche le formulaire de changement de mot de passe obligatoire.
     */
    public function showChangeForm(): View
    {
        return view('auth.password-change');
    }

    /**
     * Enregistre le nouveau mot de passe et active le compte de l'utilisateur.
     */
    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 1. Mettre à jour le mot de passe et activer le compte
        $user->password = Hash::make($request->input('password'));
        $user->status = UserStatus::ACTIVE;
        $user->save(); // Cette sauvegarde va automatiquement déclencher notre Audit Trail !

        // 2. Régénérer la session pour des raisons de sécurité
        $request->session()->regenerate();

        // 3. Rediriger avec un message de succès
        return redirect()->route('dashboard')
            ->with('success', "Votre mot de passe a été mis à jour avec succès et votre compte est désormais actif.");
    }
}
