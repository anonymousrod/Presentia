<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class D7NetworksWhatsAppService implements WhatsAppServiceInterface
{
    protected ?string $token;
    protected ?string $originator;

    public function __construct()
    {
        $this->token = config('services.d7networks.whatsapp.token');
        $this->originator = config('services.d7networks.whatsapp.originator');
    }

    /**
     * Send a WhatsApp message using D7 Networks API.
     *
     * @param string $phone
     * @param string $message
     * @return array
     * @throws \Exception
     */
    public function send(string $phone, string $message): array
    {
        if (empty($this->token)) {
            throw new \Exception("D7 Networks WhatsApp Token is not configured.");
        }

        if (empty($this->originator)) {
            throw new \Exception("D7 Networks WhatsApp Originator is not configured.");
        }

        $payload = [
            'messages' => [
                [
                    'originator' => $this->originator,
                    'content' => [
                        'message_type' => 'TEXT',
                        'text' => [
                            'preview_url' => true,
                            'body' => $message,
                        ],
                    ],
                    'recipients' => [
                        [
                            'recipient' => $phone,
                            'recipient_type' => 'individual',
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withToken($this->token)
            ->post('https://api.d7networks.com/whatsapp/v2/send', $payload);

            if ($response->failed()) {
                Log::error("D7 Networks WhatsApp send failed: " . $response->body());
                throw new \Exception(
                    "D7 Networks WhatsApp API failed with status " . $response->status() . ": " . $response->body()
                );
            }

            return $response->json() ?? ['success' => true, 'raw_response' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception in D7NetworksWhatsAppService: " . $e->getMessage());
            throw $e;
        }
    }
}
