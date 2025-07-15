<?php

return [
    'collection' => [
        'primary_key' => env('MTN_MOMO_COLLECTION_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_COLLECTION_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_COLLECTION_CALLBACK_URL'),
    ],
    
    'disbursement' => [
        'primary_key' => env('MTN_MOMO_DISBURSEMENT_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_DISBURSEMENT_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_DISBURSEMENT_CALLBACK_URL'),
    ],
    
    'remittance' => [
        'primary_key' => env('MTN_MOMO_REMITTANCE_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MOMO_REMITTANCE_SECONDARY_KEY'),
        'callback_url' => env('MTN_MOMO_REMITTANCE_CALLBACK_URL'),
    ]
];