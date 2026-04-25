<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'bank' => env('MIDTRANS_PAYMENT_BANK', 'bca'),
    'api_url' => env('MIDTRANS_API_URL', null),
];
