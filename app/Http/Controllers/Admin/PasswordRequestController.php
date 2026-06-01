<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Enums\UserStatus;
use App\Jobs\SendPasswordResetWhatsApp;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PasswordRequestController extends Controller
{
    /**
     * Liste les demandes de réinitialisation en attente.
     */
    public function index()
    {
        $requests = PasswordResetRequest::with('user')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.password-requests.index', compact('requests'));
    }

    /**
     * Valide une demande et envoie un mot de passe temporaire.
     */
    public function validateRequest(PasswordResetRequest $passwordResetRequest)
    {
        if ($passwordResetRequest->status !== 'PENDING') {
            return back()->with('error', 'Cette demande a déjà été traitée ou a expiré.');
        }

        $user = $passwordResetRequest->user;
        $tempPassword = Str::random(10);

        DB::transaction(function () use ($user, $tempPassword, $passwordResetRequest) {
            // Mettre à jour le mot de passe de l'utilisateur
            $user->update([
                'password' => Hash::make($tempPassword),
                'status' => UserStatus::PENDING, // Doit changer son mdp à la première connexion
            ]);

            // Marquer la demande comme traitée
            $passwordResetRequest->update(['status' => 'DONE']);

            // Envoyer le mdp via WhatsApp
            dispatch(new SendPasswordResetWhatsApp($user, $tempPassword));
        });

        return back()->with('success', "Le mot de passe de {$user->name} a été réinitialisé et envoyé par WhatsApp.");
    }
}
