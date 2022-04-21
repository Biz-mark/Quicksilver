<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Супербыстрый кеш для вашего статичного сайта.',
    ],
    'settings' => [
        'label' => 'Quicksilver',
        'description' => 'Различные настройки системы статичного кеша',
    ],
    'tab' => [
        'general' => 'Основное',
        'query_strings' => 'Query параметры',
    ],
    'field' => [
        'excluded' => [
            'label' => 'Исключенные пути',
            'comment' => 'Пример: acme/demo, acme/* and etc.',
            'path' => 'Путь'
        ],
        'enable_query_strings' => [
            'label' => 'Включить кеширование вместе с query параметрами.',
            'comment' => 'Quicksilver будет кешировать страницы с query параметрами как отдельные файлы.'
        ],
    ]
];
