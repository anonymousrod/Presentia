<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UltraMsgWhatsAppService implements WhatsAppServiceInterface
{
    protected string $instanceId;
    protected string $token;
    protected string $baseUrl = 'https://api.ultramsg.com';

    public function __construct()
    {
        $this->instanceId = env('ULTRAMSG_INSTANCE_ID', '');
        $this->token = env('ULTRAMSG_TOKEN', '');
    }

    /**
     * Send a WhatsApp message via Ultramsg.
     *
     * @param string $phone
     * @param string $message
     * @return array
     * @throws \Exception
     */
    public function send(string $phone, string $message): array
    {
        if (empty($this->instanceId) || empty($this->token)) {
            Log::warning("UltraMsgWhatsAppService: Credentials not set. Cannot send to {$phone}.");
            return ['error' => 'Credentials not set'];
        }

        // Ultramsg requires phone numbers in international format (e.g. +33612345678 or just 33612345678)
        // Clean the phone number to keep only digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Si le numéro a exactement 8 chiffres (ex: numéro local Bénin), on ajoute l'indicatif 229
        if (strlen($cleanPhone) === 8) {
            $cleanPhone = '229' . $cleanPhone;
        }

        $url = "{$this->baseUrl}/{$this->instanceId}/messages/chat";

        $response = Http::timeout(5)->withoutVerifying()->asForm()->post($url, [
            'token' => $this->token,
            'to' => $cleanPhone,
            'body' => $message,
        ]);

        if ($response->failed()) {
            Log::error("UltraMsgWhatsAppService Failed: " . $response->body());
            throw new \Exception("Failed to send WhatsApp message via Ultramsg: " . $response->body());
        }

        return $response->json();
    }
}
