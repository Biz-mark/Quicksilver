<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Lightning fast cache for your static website',
    ],
    'settings' => [
        'label' => 'Quicksilver',
        'description' => 'Plugin settings, adding exceptions',
        'tabs' => [
            'exceptions' => 'Exceptions',
            'clearing' => 'Clearing'
        ],
        'fields' => [
            'url_pattern' => 'Route pattern',
            'auto_clearing' => 'Auto cache clearing',
            'auto_clearing_comment' => 'Clear cache after save model of: cms/page, rainlab/post, rainlab/post-category, rainlab/static-page, rainlab/menu.',
            'blog' => [
                'post' => [
                    'label' => 'Post',
                    'comment' => 'Posts cache clearing rules',
                    'pattern' => 'URL pattern for posts',
                    'pattern_comment' => 'Select posts URL pattern',
                    'pattern_post_slug' => 'Post slug variable',
                    'pattern_post_slug_comment' => 'Write variable name of post slug (ex: slug)',
                    'pattern_category_slug' => 'Category slug variable',
                    'pattern_category_slug_comment' => 'Write variable name of category slug (ex: slug)',
                    'extra_urls' => 'Extra Urls',
                    'extra_urls_comment' => 'Urls which should be cleaned after post saving. You can use slug variable had set above like "/:slug". For recursive clearing add "*" to the end (ex: /blog/* , but url /blog will not be cleared, you should add both variants separately)',
                    'extra_urls_prompt' => 'Add url',
                    'extra_urls_label' => 'Url',
                ],
                'category' => [
                    'label' => 'Category',
                    'comment' => 'Categories cache clearing rules',
                    'pattern' => 'URL pattern for categories',
                    'pattern_comment' => 'Select categories URL pattern',
                    'pattern_slug' => 'Category slug variable',
                    'pattern_slug_comment' => 'Write variable name of slug category (ex: slug)',
                    'extra_urls' => 'Extra Urls',
                    'extra_urls_comment' => 'Urls which should be cleaned after category saving. You can use slug variable had set above like "/:slug". For recursive clearing add "*" to the end (ex: /blog/* , but url /blog will not be cleared, you should add both variants separately)',
                    'extra_urls_prompt' => 'Add url',
                    'extra_urls_label' => 'Url',
                ]
            ]
        ]
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
