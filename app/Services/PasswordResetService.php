<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Notifications\AdminPasswordResetAlert;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    /**
     * Envoie une demande de réinitialisation de mot de passe.
     *
     * @param string $identifier Email ou Téléphone
     * @return array ['success' => bool, 'message' => string, 'channel' => string|null]
     */
    public function sendResetRequest(string $identifier): array
    {
        // Rate limiting: 3 demandes max par heure par identifiant
        $throttleKey = 'password-reset:' . $identifier;
        // Règle: max 3 demandes acceptées par heure par identifiant.
        // On bloque la 4e.
        $maxAttempts = 3;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            return [
                'success' => false,
                'message' => __('passwords.throttled'),
                'channel' => null,
            ];
        }


        $user = $this->findUser($identifier);

        if (!$user) {
            // Pour des raisons de sécurité, on ne dit pas si l'utilisateur existe
            RateLimiter::hit($throttleKey, 3600);
            return [
                'success' => true,
                'message' => __('passwords.sent'),
                'channel' => null
            ];
        }

        RateLimiter::hit($throttleKey, 3600);

        if ($user->hasEmail()) {
            return $this->sendEmailReset($user);
        }

        if ($user->hasPhone()) {
            return $this->sendWhatsAppReset($user);
        }

        return [
            'success' => false,
            'message' => 'Aucun canal de communication disponible pour cet utilisateur.',
            'channel' => null
        ];
    }

    /**
     * Trouve un utilisateur par email ou téléphone.
     */
    private function findUser(string $identifier): ?User
    {
        return User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();
    }

    /**
     * Utilise le Password Broker natif de Laravel pour l'email.
     */
    private function sendEmailReset(User $user): array
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return [
                'success' => true,
                'message' => __($status),
                'channel' => 'email'
            ];
        }

        return [
            'success' => false,
            'message' => __($status),
            'channel' => 'email'
        ];
    }

    /**
     * Crée une demande WhatsApp et notifie l'admin.
     */
    private function sendWhatsAppReset(User $user): array
    {
        // Création de la requête en DB
        $request = PasswordResetRequest::create([
            'user_id' => $user->id,
            'code' => Str::random(10), // Code interne ou mdp temporaire futur ? Le ticket dit "Admin valide -> génère mdp temporaire"
            'status' => 'PENDING',
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Notifier les administrateurs
        $admins = User::role('Administrateur')->get();
        if ($admins->isEmpty()) {
            // Fallback si pas de rôle Administrateur défini par Spatie (on cherche par permission ou id 1?)
            // Selon Rolesandpermissionsseeder, il devrait y avoir des admins.
            $admins = User::whereHas('roles', function($q) { $q->where('name', 'Administrateur'); })->get();
        }

        Notification::send($admins, new AdminPasswordResetAlert($request));

        return [
            'success' => true,
            'message' => 'Votre demande a été envoyée à l\'administrateur. Vous recevrez un nouveau mot de passe par WhatsApp après validation.',
            'channel' => 'whatsapp'
        ];
    }
}
