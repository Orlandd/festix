<?php

return [
    'isProduction' => env('MIDTRANS_IS_PRODUCTION', false),
    'serverKey' => env('MIDTRANS_SERVER_KEY', 'your-server-key'),
    'clientKey' => env('MIDTRANS_CLIENT_KEY', 'your-client-key'),
    'isSanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is3ds' => env('MIDTRANS_IS_3DS', true),
];
