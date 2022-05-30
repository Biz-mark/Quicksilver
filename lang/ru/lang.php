<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Супербыстрый кэш для вашего статичного сайта.',
    ],
    'settings' => [
        'label' => 'Quicksilver',
        'description' => 'Различные настройки системы статичного кэша',
    ],
    'tab' => [
        'general' => 'Основное',
        'query_strings' => 'Query параметры',
    ],
    'field' => [
        'excluded' => [
            'label' => 'Исключенные пути',
            'comment' => 'Пример: acme/demo, acme/* и т.д.',
            'path' => 'Путь'
        ],
        'enable_query_strings' => [
            'label' => 'Включить кэширование вместе с query параметрами.',
            'comment' => 'Quicksilver будет кэшировать страницы с query параметрами как отдельные файлы.'
        ],
    ],
    'reportwidget' => [
        'label' => 'Очистка кэша Quicksilver',
        'clear_specific' => 'Кэш :path успешно очищен.',
        'clear_all_paths' => 'Кэш страниц успешно очищен.',
        'clear_all' => 'Системный кэш и страницы успешно очищены.',
        'title' => 'Quicksilver кэш',
        'clearing_path' => 'Очищаемый путь',
        'clearing_example' => 'Например: /path, /path/something, /path/*',
        'clear_path' => 'Очистить страницы по указанному пути',
        'clear_all_pages' => 'Очистить все страницы',
        'clear_all_pages_confirm' => 'Уверены что хотите удалить все страницы?',
        'clear_all_caches' => 'Очистить системный кеш и страницы',
        'clear_all_caches_confirm' => 'Очистить системный кеш и страницы',
    ]
];
