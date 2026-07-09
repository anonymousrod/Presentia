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

class SendWhatsAppCredentials implements ShouldQueue
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
        public readonly string $plainPassword
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppServiceInterface $whatsApp): void
    {
        if (! $this->user->phone) {
            Log::warning("SendWhatsAppCredentials: utilisateur {$this->user->id} sans numéro de téléphone. Job abandonné.");
            return;
        }

        $loginUrl = route('login');

        $message = "👋 Bonjour {$this->user->first_name},\n\n"
            . "Bienvenue sur " . config('app.name') . " ! Votre compte a été créé avec succès par l'administrateur.\n\n"
            . "Voici vos identifiants temporaires :\n"
            . "📞 *Identifiant (Téléphone)* : {$this->user->phone}\n"
            . "🔑 *Mot de passe temporaire* : {$this->plainPassword}\n\n"
            . "🔗 *Lien de connexion* : {$loginUrl}\n\n"
            . "Veuillez vous connecter à l'application et changer votre mot de passe lors de votre première connexion.";

        try {
            $response = $whatsApp->send($this->user->phone, $message);

            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'credentials',
                'status'            => 'sent',
                'provider_response' => $response,
            ]);
        } catch (\Throwable $e) {
            WhatsappLog::create([
                'user_id'           => $this->user->id,
                'message_type'      => 'credentials',
                'status'            => 'failed',
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }
}
