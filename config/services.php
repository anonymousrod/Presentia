<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'd7networks' => [
        'whatsapp' => [
            'token' => env('D7NETWORKS_WHATSAPP_TOKEN'),
            'originator' => env('D7NETWORKS_WHATSAPP_ORIGINATOR'),
        ],
    ],

    'whatsapp' => [
        'from-phone-number-id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
        'token'                => env('WHATSAPP_TOKEN'),
    ],

    'meta_whatsapp' => [
        // Phone Number ID trouvé dans : Meta Business Manager > Comptes WhatsApp > Numéros de téléphone
        'phone_number_id' => env('META_WHATSAPP_PHONE_NUMBER_ID'),
        // Token d'accès permanent (System User Token) ou token temporaire de 24h (développement)
        'access_token'    => env('META_WHATSAPP_ACCESS_TOKEN'),
        // Version de l'API Graph Meta — mettre à jour si Meta publie une nouvelle version LTS
        'api_version'     => env('META_WHATSAPP_API_VERSION', 'v20.0'),
    ],

];
