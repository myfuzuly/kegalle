<?php

return [

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', env('APP_URL').'/auth/google/callback'),
        'translate_key' => env('GOOGLE_TRANSLATE_API_KEY', ''),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env(
            'FACEBOOK_REDIRECT_URI',
            env('APP_URL').'/auth/facebook/callback'
        ),
    ],

    'webpush' => [
        'public_key'  => env('WEBPUSH_PUBLIC_KEY',  ''),
        'private_key' => str_replace('\n', "\n", env('WEBPUSH_PRIVATE_KEY', '')),
        'subject'     => env('WEBPUSH_SUBJECT',     'mailto:admin@kegalle.com'),
    ],

    'hutch_sms' => [
        'url'       => env('HUTCH_SMS_URL',       'https://api.hutch.lk/api/sms/send'),
        'username'  => env('HUTCH_SMS_USERNAME',  ''),
        'password'  => env('HUTCH_SMS_PASSWORD',  ''),
        'sender_id' => env('HUTCH_SMS_SENDER_ID', 'kegalle'),
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY', ''),
    ],

    'google_translate' => [
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY', ''),
    ],

    'analytics' => [
        'ga_id'    => env('GA_MEASUREMENT_ID', ''),
        'pixel_id' => env('META_PIXEL_ID', ''),
    ],

];
