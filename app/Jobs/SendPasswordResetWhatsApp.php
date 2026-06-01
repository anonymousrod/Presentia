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

        $message = "Votre mot de passe a été réinitialisé par l'administrateur. Votre nouveau mot de passe temporaire est : {$this->temporaryPassword}. Veuillez le changer dès votre première connexion.";

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

            throw $e;
        }
    }
}
