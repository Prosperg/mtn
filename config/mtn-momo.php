<?php

return [
    'base_uri' => env('MTN_MOMO_BASE_URI', 'https://sandbox.momodeveloper.mtn.com'),
    
    'collection' => [
        'primary_key' => env('MTN_MOMO_COLLECTION_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_COLLECTION_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_COLLECTION_CALLBACK_URL'),
        'user_id' => env('MTN_MOMO_COLLECTION_USER_ID'),
        'api_key' => env('MTN_MOMO_COLLECTION_API_KEY'),
    ],
    
    'disbursement' => [
        'primary_key' => env('MTN_MOMO_DISBURSEMENT_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_DISBURSEMENT_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_DISBURSEMENT_CALLBACK_URL'),
        'user_id' => env('MTN_MOMO_DISBURSEMENT_USER_ID'),
        'api_key' => env('MTN_MOMO_DISBURSEMENT_API_KEY'),
    ],
    
    'remittance' => [
        'primary_key' => env('MTN_MOMO_REMITTANCE_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_REMITTANCE_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_REMITTANCE_CALLBACK_URL'),
        'user_id' => env('MTN_MOMO_REMITTANCE_USER_ID'),
        'api_key' => env('MTN_MOMO_REMITTANCE_API_KEY'),
    ],
    
    'timeout' => env('MTN_MOMO_TIMEOUT', 30),
];