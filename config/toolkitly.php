<?php

return [
    'max_upload_kb' => (int) env('TOOLKITLY_MAX_UPLOAD_KB', 10240),
    'temporary_file_ttl' => (int) env('TOOLKITLY_TEMPORARY_FILE_TTL', 3600),
    'adsense' => [
        'client' => env('GOOGLE_ADSENSE_CLIENT'),
        'slots' => [
            'top' => env('GOOGLE_ADSENSE_SLOT_TOP'),
            'bottom' => env('GOOGLE_ADSENSE_SLOT_BOTTOM'),
        ],
    ],
    'analytics' => [
        'google_measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID'),
        'dashboard_token' => env('TOOLKITLY_ANALYTICS_TOKEN'),
    ],
];
