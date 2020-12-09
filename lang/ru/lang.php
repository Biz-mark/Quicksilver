<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Супербыстрый кеш для вашего статичного сайта.',
    ],
    'settings' => [
        'label' => 'Quicksilver',
        'description' => 'Настройки плагина, добавление исключений',
        'tab' => 'Исключения',
        'fields' => [
            'url_pattern' => 'Путь/URL (Допускается использование регулярных выражений)',
        ]
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
                'all' => 'Очистить весь кэш',
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
