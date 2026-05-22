<?php

namespace App\Services;

class FakeWhatsAppService implements WhatsAppServiceInterface
{
    protected static array $sentMessages = [];
    protected static int $shouldFailAttempts = 0;
    protected static ?\Throwable $failureException = null;

    /**
     * Reset the mock state.
     */
    public static function reset(): void
    {
        self::$sentMessages = [];
        self::$shouldFailAttempts = 0;
        self::$failureException = null;
    }

    /**
     * Configure the next N send attempts to fail.
     */
    public static function failNextAttempts(int $attempts, ?\Throwable $exception = null): void
    {
        self::$shouldFailAttempts = $attempts;
        self::$failureException = $exception ?: new \Exception("Simulated WhatsApp API Gateway Error");
    }

    /**
     * Retrieve the list of messages processed successfully.
     */
    public static function getSentMessages(): array
    {
        return self::$sentMessages;
    }

    /**
     * Mock send implementation.
     *
     * @param string $phone
     * @param string $message
     * @return array
     * @throws \Throwable
     */
    public function send(string $phone, string $message): array
    {
        if (self::$shouldFailAttempts > 0) {
            self::$shouldFailAttempts--;
            throw self::$failureException ?: new \Exception("Simulated WhatsApp API Gateway Error");
        }

        $response = [
            'success' => true,
            'message_id' => 'wa_' . uniqid(),
            'recipient' => $phone,
            'body' => $message,
            'provider' => 'fake_d7networks',
        ];

        self::$sentMessages[] = [
            'phone' => $phone,
            'message' => $message,
            'response' => $response,
        ];

        return $response;
    }
}
