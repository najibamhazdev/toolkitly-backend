<?php

return [
    'max_upload_kb' => (int) env('TOOLKITLY_MAX_UPLOAD_KB', 10240),
    'temporary_file_ttl' => (int) env('TOOLKITLY_TEMPORARY_FILE_TTL', 3600),
];
