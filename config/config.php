<?php

return [
    'default' => env('QS_DEFAULT_DRIVER', 'quicksilver'),
    'disks' => [
        'quicksilver' => [
            'driver' => env('QS_DISK_DRIVER', 'local'),
            'root' => env('QS_DISK_ROOT', storage_path('page-cache'))
        ]
    ],
    'contentTypes' => [
        'application/json' => '.json',
        'application/x-www-form-urlencoded' => '.json',
        'application/atom+xml' => '.xml',
        'application/xml' => '.xml',
        'text/plain' => '.txt',
        'text/html' => '.html'
    ]
];
