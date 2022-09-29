<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Quicksilver disk
    |--------------------------------------------------------------------------
    |
    | Specifies the default storage disk used for storing page caches
    |
    | Supported disks: "quicksilver", "local" and etc.
    |
    */

    'default' => env('QS_DEFAULT_DISK', 'quicksilver'),

    /*
    |--------------------------------------------------------------------------
    | Quicksilver storage disks
    |--------------------------------------------------------------------------
    |
    | As for default disk, quicksilver utilizes page-cache folder inside
    | storage directory. You can add any other disks that you can
    | use as a cache storage.
    |
    */

    'disks' => [

        'quicksilver' => [
            'driver' => env('QS_DISK_DRIVER', 'local'),
            'root' => env('QS_DISK_ROOT', storage_path('quicksilver'))
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Quicksilver content types
    |--------------------------------------------------------------------------
    |
    | Quicksilver is able to distinguish the mime-type of pages and store
    | them as separate files, based on response content-type header.
    | This config allows you to extend this behavior.
    |
    */

    'contentTypes' => [
        'application/json' => 'json',
        'application/x-www-form-urlencoded' => 'json',
        'application/xml' => 'xml',
        'application/atom+xml' => 'xml',
        'text/plain' => 'txt',
        'text/html' => 'html'
    ],


    /*
    |--------------------------------------------------------------------------
    | Default Quicksilver headers
    |--------------------------------------------------------------------------
    |
    | Customize headers added to QuickSilver response
    |
    | If you set any header as empty in your .env (like QS_CACHE_CONTROL=)
    | the header won't be added to the response at all
    |
    */

    'defaultHeaders' => [
        'Cache-Control' => env('QS_CACHE_CONTROL', 'public, max-age=7200')
    ],

];
