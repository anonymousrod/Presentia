<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\WhatsAppServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendActivityReminder implements ShouldQueue
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
        public readonly Activity $activity
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppServiceInterface $whatsApp): void
    {
        if (! $this->user->phone) {
            Log::warning("SendActivityReminder: utilisateur {$this->user->id} sans numéro de téléphone. Job abandonné.");
            return;
        }

        $startTime = $this->activity->start_time
            ? $this->activity->start_time->translatedFormat('l d F Y à H:i')
            : 'date à confirmer';

        $location = $this->activity->location ?? 'lieu à confirmer';

        $message = "Rappel Presentia 📅\n\n"
            . "Bonjour {$this->user->first_name},\n\n"
            . "Nous vous rappelons que l'activité suivante approche :\n\n"
            . "📌 *{$this->activity->title}*\n"
            . "🗓 {$startTime}\n"
            . "📍 {$location}\n\n"
            . "Merci d'être ponctuel(le). À bientôt !";

        try {
            $response = $whatsApp->send($this->user->phone, $message);

            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'reminder',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Throwable $e) {
            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'reminder',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }
}
