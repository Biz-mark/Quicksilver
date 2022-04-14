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
            'label' => 'Path',
            'comment' => 'Example: acme/demo, acme/* and etc.',
            'path' => 'Path'
        ]
//        'enable_query_strings' => [
//            'label' => 'Enable query strings support',
//            'comment' => 'Quicksilver will cache page with different query strings as separate entities.'
//        ],
//        'query_strings_mode' => [
//            'label' => 'Choose mode',
//            'comment' => 'Everything: cache every query string. Except: Cache everything except... Only: Cache only specified query strings'
//        ],
//        'except_section' => 'In this mode you can define which routes will have ',
    ]
];
