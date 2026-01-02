<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Lightning-fast caching for static websites',
    ],

    'settings' => [
        'label' => 'Quicksilver Settings',
        'description' => 'Configuration options for the cache processor',
    ],

    'tab' => [
        'general' => 'General',
        'query_strings' => 'Query Strings',
    ],

    'field' => [
        'excluded' => [
            'label' => 'Excluded Paths',
            'comment' => 'Examples: acme/demo, acme/*, etc.',
            'path' => 'Path',
        ],

        'enable_query_strings' => [
            'label' => 'Enable Query String Support',
            'comment' => 'Quicksilver will cache pages with different query strings as separate entries.',
        ],
    ],

    'reportwidget' => [
        'label' => 'Quicksilver Cache Cleaner',
        'title' => 'Quicksilver Cache',

        'clear_specific' => 'Cache for :path has been successfully cleared.',
        'clear_all_paths' => 'Cache for all pages has been successfully cleared.',
        'clear_all' => 'System cache and all pages have been successfully cleared.',

        'clearing_path' => 'Path to clear',
        'clearing_example' => 'Example: /path, /path/subpath, /path/*',

        'clear_path' => 'Clear cached pages at the specified path',
        'clear_all_pages' => 'Clear cache for all pages',
        'clear_all_pages_confirm' => 'Are you sure you want to delete all cached pages?',
        'clear_all_caches' => 'Clear system cache and all cached pages',
        'clear_all_caches_confirm' => 'Are you sure you want to clear all caches?',
    ],
];
