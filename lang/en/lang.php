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
    ],
    'field' => [
        'cache_query_strings' => [
            'label' => 'Enable query strings support',
            'comment' => 'Quicksilver will cache page with different query strings as separate instances.'
        ]
    ]
];
