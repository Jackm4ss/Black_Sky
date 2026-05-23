<?php

return [
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:102400'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png',
            'gif',
            'bmp',
            'jpg',
            'jpeg',
            'webp',
        ],
        'max_upload_time' => 30,
        'cleanup' => true,
    ],
];
