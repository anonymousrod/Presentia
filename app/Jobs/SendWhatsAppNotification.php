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

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * Nombre maximum de tentatives.
     */
    public int $tries = 3;

    /**
     * Délais de backoff exponentiel en secondes : 30s, 60s, 120s.
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function __construct(
        public readonly User $user,
        public readonly string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppServiceInterface $whatsApp): void
    {
        if (! $this->user->phone) {
            Log::warning("SendWhatsAppNotification: utilisateur {$this->user->id} sans numéro de téléphone. Job abandonné.");
            return;
        }

        try {
            $response = $whatsApp->send($this->user->phone, $this->message);

            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'notification',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Throwable $e) {
            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'notification',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }
}
