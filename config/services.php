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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'tinymce' => [
        'api_key' => env('TINYMCE_API_KEY'),
    ],

    'ifrs' => [
        'enabled' => env('IFRS_LOGIN_ENABLED', env('IFRS_CLIENT_ID') && env('IFRS_CLIENT_SECRET')),
        'client_id' => env('IFRS_CLIENT_ID'),
        'client_secret' => env('IFRS_CLIENT_SECRET'),
        'redirect' => env('IFRS_REDIRECT_URI'),
        'base_url' => env('IFRS_BASE_URL'),
        'user_endpoint' => env('IFRS_USER_ENDPOINT'),
    ],

];
