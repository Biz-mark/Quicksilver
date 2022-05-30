<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Lightning fast cache for your static website'
    ],
    'settings' => [
        'label' => 'Quicksilver settings',
        'description' => 'Different settings of cache processor',
    ],
    'tab' => [
        'general' => 'General',
        'query_strings' => 'Query strings',
    ],
    'field' => [
        'excluded' => [
            'label' => 'Excluded paths',
            'comment' => 'Example: acme/demo, acme/* and etc.',
            'path' => 'Path'
        ],
        'enable_query_strings' => [
            'label' => 'Enable query strings support',
            'comment' => 'Quicksilver will cache page with different query strings as separate entities.'
        ],
    ],
    'reportwidget' => [
        'label' => 'Quicksilver cache clearer',
        'clear_specific' => 'Cache of :path successfully cleared.',
        'clear_all_paths' => 'Cache of all pages successfully cleared.',
        'clear_all' => 'System cache and all pages successfully cleared.',
        'title' => 'Quicksilver cache',
        'clearing_path' => 'Path to clear',
        'clearing_example' => 'Example: /path, /path/something, /path/*',
        'clear_path' => 'Clear pages cache at specified path',
        'clear_all_pages' => 'Clear all pages cache',
        'clear_all_pages_confirm' => 'Are you sure you want to delete all cached pages?',
        'clear_all_caches' => 'Clear system cache and all cached pages',
        'clear_all_caches_confirm' => 'Are you sure you want to clear all caches?',
    ]
];
