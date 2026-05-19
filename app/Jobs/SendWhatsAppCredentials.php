<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppCredentials implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    public User $user;
    public string $plainPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        if ($this->user->phone) {
            $message = "Bonjour {$this->user->first_name},\n\nVotre compte Presentia a été créé par l'administrateur.\n\nVoici vos identifiants temporaires :\n- Identifiant (Téléphone) : {$this->user->phone}\n- Mot de passe temporaire : {$this->plainPassword}\n\nVeuillez vous connecter à l'application et changer votre mot de passe lors de votre première connexion.";

            $whatsAppService->sendMessage($this->user, $message, 'credentials');
        }
    }
}
