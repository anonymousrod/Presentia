<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Implémentation de l'API Cloud WhatsApp officielle de Meta (WhatsApp Business Platform).
 *
 * Configuration requise dans .env :
 *   WHATSAPP_DRIVER=meta
 *   META_WHATSAPP_PHONE_NUMBER_ID=123456789012345   ← l'ID du numéro dans Meta Business
 *   META_WHATSAPP_ACCESS_TOKEN=EAAxxxxxxxxxxxxxxx   ← token permanent (System User) ou temporaire
 *   META_WHATSAPP_API_VERSION=v20.0                 ← version Graph API (optionnel, défaut v20.0)
 *
 * Documentation : https://developers.facebook.com/docs/whatsapp/cloud-api/messages/text-messages
 */
class MetaCloudWhatsAppService implements WhatsAppServiceInterface
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $apiVersion;
    protected string $baseUrl;

    public function __construct()
    {
        $this->phoneNumberId = config('services.meta_whatsapp.phone_number_id', '');
        $this->accessToken   = config('services.meta_whatsapp.access_token', '');
        $this->apiVersion    = config('services.meta_whatsapp.api_version', 'v20.0');
        $this->baseUrl       = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
    }

    /**
     * Envoie un message texte libre via l'API Cloud Meta WhatsApp.
     *
     * ⚠️  Règle des 24h de Meta : un message texte libre (non-template) ne peut être
     *     envoyé que dans une fenêtre de 24h APRÈS que le destinataire vous a écrit
     *     en premier. En dehors de cette fenêtre, utilisez un template approuvé.
     *
     *     Pour Presentia, la grande majorité des messages est déclenchée par une
     *     action du membre (confirmation de présence, paiement, demande de reset).
     *     Ils tombent donc dans la fenêtre de service → pas de template requis.
     *
     *     Les messages proactifs (rappels d'activité 24h avant, création de compte)
     *     passent par la catégorie "utility" qui ne nécessite PAS de fenêtre ouverte
     *     mais REQUIERT un template approuvé par Meta.
     *
     *     Pour le démarrage, TOUS les messages sont envoyés en texte libre
     *     (type: text). Dès que les templates Meta sont approuvés, les jobs
     *     correspondants pourront être mis à jour pour utiliser sendTemplate().
     *
     * @param string $phone  Numéro international sans "+" ex: "22997000000"
     * @param string $message Corps du message (formatage *gras* supporté via WhatsApp)
     * @return array Réponse JSON de l'API Meta
     * @throws \Exception En cas d'échec HTTP ou de configuration manquante
     */
    public function send(string $phone, string $message): array
    {
        $this->validateConfiguration();

        $cleanPhone = $this->normalizePhone($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $cleanPhone,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $message,
            ],
        ];

        try {
            $response = $this->http()->post($this->baseUrl, $payload);

            $json = $response->json() ?? [];

            if ($response->failed()) {
                $error = $json['error']['message'] ?? $response->body();
                $code  = $json['error']['code'] ?? $response->status();

                Log::error("MetaCloudWhatsAppService: Échec envoi vers {$cleanPhone}. Code: {$code}. Message: {$error}");

                throw new \Exception(
                    "Meta WhatsApp API failed (HTTP {$response->status()}, code {$code}): {$error}"
                );
            }

            Log::info("MetaCloudWhatsAppService: Message envoyé vers {$cleanPhone}. Message ID: " . ($json['messages'][0]['id'] ?? 'N/A'));

            return $json;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("MetaCloudWhatsAppService: Timeout/connexion impossible. " . $e->getMessage());
            throw new \Exception("Meta WhatsApp API: connexion impossible. " . $e->getMessage());
        }
    }

    /**
     * Envoie un message via un template approuvé par Meta.
     *
     * À utiliser pour les messages PROACTIFS hors fenêtre de 24h
     * (ex: rappels d'activité, diffusions pastorales, création de compte).
     *
     * @param string $phone           Numéro international sans "+"
     * @param string $templateName    Nom du template approuvé dans Meta Business Manager
     * @param string $languageCode    Code langue du template (ex: "fr", "fr_FR")
     * @param array  $components      Composants du template (variables {{1}}, {{2}}, etc.)
     * @return array Réponse JSON de l'API Meta
     * @throws \Exception
     */
    public function sendTemplate(
        string $phone,
        string $templateName,
        string $languageCode = 'fr',
        array $components = []
    ): array {
        $this->validateConfiguration();

        $cleanPhone = $this->normalizePhone($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $cleanPhone,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $languageCode],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = $this->http()->post($this->baseUrl, $payload);

            $json = $response->json() ?? [];

            if ($response->failed()) {
                $error = $json['error']['message'] ?? $response->body();
                $code  = $json['error']['code'] ?? $response->status();

                Log::error("MetaCloudWhatsAppService::sendTemplate: Échec. Template: {$templateName}, Code: {$code}, Message: {$error}");

                throw new \Exception(
                    "Meta WhatsApp Template API failed (HTTP {$response->status()}, code {$code}): {$error}"
                );
            }

            Log::info("MetaCloudWhatsAppService::sendTemplate: Template '{$templateName}' envoyé vers {$cleanPhone}.");

            return $json;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("MetaCloudWhatsAppService::sendTemplate: Timeout. " . $e->getMessage());
            throw new \Exception("Meta WhatsApp Template API: connexion impossible. " . $e->getMessage());
        }
    }

    /**
     * Normalise un numéro de téléphone pour l'API Meta.
     * Prend en charge les formats internationaux, locaux Bénin 8 chiffres et 10 chiffres (avec ou sans 01).
     */
    public function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Format local Bénin 8 chiffres (ex: 69129089) -> 22969129089
        if (strlen($clean) === 8) {
            return '229' . $clean;
        }

        // Format local Bénin 10 chiffres (ex: 0169129089) -> 2290169129089
        if (strlen($clean) === 10 && str_starts_with($clean, '01')) {
            return '229' . $clean;
        }

        return $clean;
    }

    /**
     * Crée une instance client HTTP configurée pour l'API Meta.
     */
    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        $http = Http::timeout(10)
            ->withToken($this->accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]);

        // Désactiver la vérification SSL en local (contourne le problème cacert sous Windows)
        if (app()->environment('local', 'testing')) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }

    /**
     * Vérifie que les clés de configuration obligatoires sont bien renseignées.
     *
     * @throws \Exception
     */
    private function validateConfiguration(): void
    {
        if (empty($this->phoneNumberId)) {
            throw new \Exception(
                'MetaCloudWhatsAppService: META_WHATSAPP_PHONE_NUMBER_ID est manquant dans le .env.'
            );
        }

        if (empty($this->accessToken)) {
            throw new \Exception(
                'MetaCloudWhatsAppService: META_WHATSAPP_ACCESS_TOKEN est manquant dans le .env.'
            );
        }
    }
}
