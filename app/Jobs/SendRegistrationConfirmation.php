<?php

namespace App\Jobs;

use App\Models\Registration;
use App\Models\WhatsappLog;
use App\Services\WhatsAppServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRegistrationConfirmation implements ShouldQueue
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

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Registration $registration
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppServiceInterface $whatsApp): void
    {
        $registration = $this->registration->loadMissing(['user', 'activity']);
        $user = $registration->user;
        $activity = $registration->activity;

        if (! $user->phone) {
            Log::warning("SendRegistrationConfirmation: utilisateur {$user->id} sans numéro de téléphone. Job abandonné.");
            return;
        }

        $startTime = $activity->start_time
            ? $activity->start_time->translatedFormat('l d F Y à H:i')
            : 'date à confirmer';

        $location = $activity->location ?? 'lieu à confirmer';
        $statusStr = '';
        $isWaitlisted = $registration->is_waitlisted;

        if ($isWaitlisted) {
            $statusStr = "Inscrit(e) sur Liste d'attente ⏳";
        } else {
            $statusVal = $registration->status instanceof \App\Enums\RegistrationStatus 
                ? $registration->status->value 
                : $registration->status;

            switch ($statusVal) {
                case 'PRESENT':
                    $statusStr = "Inscrit(e) ✅";
                    break;
                case 'UNCERTAIN':
                    $statusStr = "Incertain(e) ❓";
                    break;
                case 'ABSENT_JUSTIFIED':
                    $statusStr = "Désinscrit(e)/Absent(e) ❌";
                    break;
                default:
                    $statusStr = $statusVal;
                    break;
            }
        }

        $message = "Confirmation Presentia 📅\n\n"
            . "Bonjour {$user->first_name},\n\n"
            . "Votre statut pour l'activité suivante a été mis à jour :\n\n"
            . "📌 *{$activity->title}*\n"
            . "🗓 {$startTime}\n"
            . "📍 {$location}\n"
            . "Statut : *{$statusStr}*\n";

        if (!$isWaitlisted && $registration->status === \App\Enums\RegistrationStatus::ABSENT_JUSTIFIED && $registration->justification) {
            $message .= "Motif : {$registration->justification}\n";
        }

        $message .= "\nMerci pour votre réactivité. À bientôt !";

        try {
            $response = $whatsApp->send($user->phone, $message);

            WhatsappLog::create([
                'user_id'           => $user->id,
                'message_type'      => 'registration_confirmation',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Throwable $e) {
            WhatsappLog::create([
                'user_id'           => $user->id,
                'message_type'      => 'registration_confirmation',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }
}
