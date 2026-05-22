<?php

namespace App\Services;

interface WhatsAppServiceInterface
{
    /**
     * Send a WhatsApp message.
     *
     * @param string $phone The recipient's phone number.
     * @param string $message The message body.
     * @return array The response details from the provider.
     * @throws \Throwable
     */
    public function send(string $phone, string $message): array;
}
