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

    'ifrs_login' => [
        'enabled' => env('IFRS_LOGIN_ENABLED', false),
        'client_id' => env('IFRS_LOGIN_CLIENT_ID'),
        'client_secret' => env('IFRS_LOGIN_CLIENT_SECRET'),
        'redirect' => env('IFRS_LOGIN_REDIRECT_URI', env('APP_URL').'/login/ifrs/callback'),
        'authorize_url' => env('IFRS_LOGIN_AUTHORIZE_URL'),
        'token_url' => env('IFRS_LOGIN_TOKEN_URL'),
        'userinfo_url' => env('IFRS_LOGIN_USERINFO_URL'),
        'scopes' => env('IFRS_LOGIN_SCOPES', 'openid profile email'),
    ],

];
