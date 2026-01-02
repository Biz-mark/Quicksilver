<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Quicksilver Disk
    |--------------------------------------------------------------------------
    |
    | Specifies the default storage disk used to store cached pages.
    |
    | Supported disks include "quicksilver", "local", and any other
    | disks defined in this configuration.
    |
    */

    'default' => env('QS_DEFAULT_DISK', 'quicksilver'),

    /*
    |--------------------------------------------------------------------------
    | Quicksilver Storage Disks
    |--------------------------------------------------------------------------
    |
    | The default Quicksilver disk uses a dedicated cache directory
    | inside the application's storage folder.
    |
    | You may define additional disks here to use as cache storage.
    |
    */

    'disks' => [

        'quicksilver' => [
            'driver' => env('QS_DISK_DRIVER', 'local'),
            'root'   => env('QS_DISK_ROOT', storage_path('quicksilver')),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Quicksilver Content Types
    |--------------------------------------------------------------------------
    |
    | Quicksilver can detect the MIME type of responses and store cached
    | pages using different file extensions based on the Content-Type header.
    |
    | This configuration allows you to extend or override this behavior.
    |
    */

    'contentTypes' => [
        'application/json'                  => 'json',
        'application/x-www-form-urlencoded' => 'json',
        'application/xml'                   => 'xml',
        'application/atom+xml'              => 'xml',
        'text/plain'                        => 'txt',
        'text/html'                         => 'html',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Quicksilver Headers
    |--------------------------------------------------------------------------
    |
    | Defines additional headers added to cached responses.
    |
    | If a header is set to an empty value in the environment file
    | (for example: QS_CACHE_CONTROL=), it will not be added at all.
    |
    */

    'defaultHeaders' => [
        'Cache-Control' => env('QS_CACHE_CONTROL', 'public, max-age=7200'),
    ],

];
