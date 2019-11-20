<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Lightning fast cache for your static website',
    ],
    'reportwidget' => [
        'cachestatus' => [
            'name' => 'Cache status',
            'clearing' => 'Clearing',
            'title' => [
                'cachedpages' => 'Pages cache',
                'pagesweight' => 'Pages weight',
                'cachedoctober' => 'October cache',
                'octoberweight' => 'October cache weight',
            ],
            'clear' => [
                'all' => 'Purge all cache',
                'pages' => 'Purge pages cache',
            ],
            'flash' => [
                'all_cleared_success' => 'All cache cleared successfully',
                'pages_cleared_success' => 'Pages cache cleared successfully'
            ],
            'files' => 'files',
            'mb' => 'mb.',
        ]
    ]
];