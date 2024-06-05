<?php

return [
    'sandbox' => [
        'token_url' => 'https://sandbox.sms.fpt.net/oauth2/token',
        'sms_url' => 'http://sandbox.sms.fpt.net/api/push-brandname-otp',
        'client_id' => env('SMS_SANDBOX_CLIENT_ID', 'sandbox-client-id'),
        'client_secret' => env('SMS_SANDBOX_CLIENT_SECRET', 'sandbox-client-secret'),
    ],
    'production' => [
        'token_url' => 'https://api01.sms.fpt.net/oauth2/token',
        'sms_url' => 'https://api01.sms.fpt.net/api/push-brandname-otp',
        'client_id' => env('SMS_PRODUCTION_CLIENT_ID', 'production-client-id'),
        'client_secret' => env('SMS_PRODUCTION_CLIENT_SECRET', 'production-client-secret'),
    ],
    'brand_name' => env('SMS_BRAND_NAME', 'XINTEL.VN'),
];
