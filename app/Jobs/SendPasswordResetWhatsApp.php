<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\WhatsAppServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPasswordResetWhatsApp implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function __construct(
        public readonly User $user,
        public readonly string $temporaryPassword
    ) {
    }

    public function handle(WhatsAppServiceInterface $whatsApp): void
    {
        if (! $this->user->phone) {
            Log::warning("SendPasswordResetWhatsApp: utilisateur {$this->user->id} sans numéro de téléphone.");
            return;
        }

        $appName = config('app.name', 'Presentia');
        $name = $this->user->first_name ?? $this->user->name ?? 'Membre';

        $message = "👋 *Bonjour {$name},*

🔐 *{$appName} — Mot de passe temporaire*

Votre mot de passe a été réinitialisé suite à votre demande sur la plateforme {$appName}.

🔑 *Nouveau mot de passe temporaire :* {$this->temporaryPassword}

👉 Connectez-vous sur la plateforme et modifiez ce mot de passe dès votre première connexion pour sécuriser votre compte.";

        try {
            $response = $whatsApp->send($this->user->phone, $message);

            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'password_reset',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Throwable $e) {
            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'password_reset',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            Log::error("SendPasswordResetWhatsApp Error: " . $e->getMessage());
        }
    }
}
