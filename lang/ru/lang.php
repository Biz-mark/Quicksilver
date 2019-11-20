<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Супербыстрый кеш для вашего статичного сайта.',
    ],
    'reportwidget' => [
        'cachestatus' => [
            'name' => 'Статус кэша',
            'clearing' => 'Очистка',
            'title' => [
                'cachedpages' => 'Кэш страниц',
                'pagesweight' => 'Вес страниц',
                'cachedoctober' => 'Кэш October',
                'octoberweight' => 'Вес кэша October',
            ],
            'clear' => [
                'all' => 'Чистка всего кэш',
                'pages' => 'Очистить кэш страниц',
            ],
            'flash' => [
                'all_cleared_success' => 'Весь кэш был успешно очищен',
                'pages_cleared_success' => 'Кэш страниц был успешно очищен'
            ],
            'files' => 'файл(ов)',
            'mb' => 'мб.',
        ]
    ]
];