<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envoie un message WhatsApp à l'utilisateur et enregistre le log.
     */
    public function sendMessage(User $user, string $message, string $messageType = 'notification'): bool
    {
        if (! $user->phone) {
            Log::warning("Impossible d'envoyer un message WhatsApp : l'utilisateur {$user->id} n'a pas de numéro de téléphone.");
            return false;
        }

        // Simulation de l'envoi WhatsApp (Twilio / Meta API mock)
        Log::info("WhatsApp envoyé à {$user->phone} [Type: {$messageType}] : {$message}");

        // Enregistrement du log d'envoi en DB
        WhatsappLog::create([
            'user_id'           => $user->id,
            'message_type'      => $messageType,
            'status'            => 'sent',
            'provider_response' => [
                'success'    => true,
                'message_id' => 'wa_' . uniqid(),
                'recipient'  => $user->phone,
                'body'       => $message,
                'provider'   => 'mock_gateway',
            ],
        ]);

        return true;
    }
}
