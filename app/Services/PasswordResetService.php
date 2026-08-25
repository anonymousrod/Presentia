<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Enums\UserStatus;
use App\Notifications\Admin\PasswordResetDoneNotification;
use App\Jobs\SendPasswordResetWhatsApp;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        // Rate limiting: 3 demandes max par heure par identifiant (10 en environnement local)
        $throttleKey = 'password-reset:' . $identifier;
        $maxAttempts = app()->environment('local') ? 10 : 3;

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

        $isEmailInput = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;

        if ($isEmailInput) {
            if ($user->hasEmail()) {
                return $this->sendEmailReset($user);
            }
            if ($user->hasPhone()) {
                return $this->sendWhatsAppReset($user);
            }
        } else {
            // L'utilisateur a saisi un numéro de téléphone !
            if ($user->hasPhone()) {
                return $this->sendWhatsAppReset($user);
            }
            if ($user->hasEmail()) {
                return $this->sendEmailReset($user);
            }
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
     * Réinitialisation WhatsApp 100% automatique et instantanée.
     */
    private function sendWhatsAppReset(User $user): array
    {
        $tempPassword = Str::random(10);

        DB::transaction(function () use ($user, $tempPassword) {
            // 1. Mettre à jour le mot de passe et basculer le statut en PENDING (changement obligatoire)
            $user->update([
                'password' => Hash::make($tempPassword),
                'status'   => UserStatus::PENDING,
            ]);

            // 2. Enregistrer la demande comme traitée immédiatement
            PasswordResetRequest::create([
                'user_id'    => $user->id,
                'code'       => 'AUTO',
                'status'     => 'DONE',
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            // 3. Expédier le mot de passe temporaire directement par WhatsApp
            dispatch(new SendPasswordResetWhatsApp($user, $tempPassword));

            // 4. Notification système
            $user->notify(new PasswordResetDoneNotification());
        });

        return [
            'success' => true,
            'message' => 'Un mot de passe temporaire vient de vous être envoyé par WhatsApp.',
            'channel' => 'whatsapp'
        ];
    }
}
