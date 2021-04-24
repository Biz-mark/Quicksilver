<?php

return [
    'plugin' => [
        'name' => 'Quicksilver',
        'description' => 'Супербыстрый кеш для вашего статичного сайта.',
    ],
    'settings' => [
        'label' => 'Quicksilver',
        'description' => 'Настройки плагина, добавление исключений',
        'tabs' => [
            'exceptions' => 'Исключения',
            'clearing' => 'Очистка'
        ],
        'fields' => [
            'url_pattern' => 'Путь/URL (Допускается использование регулярных выражений)',
            'auto_clearing' => 'Автоочистка кеша',
            'auto_clearing_comment' => 'Очистка кеша после сохранения модели: cms/page, rainlab/post, rainlab/post-category, rainlab/static-page, rainlab/menu.',
            'blog' => [
                'post' => [
                    'label' => 'Пост',
                    'comment' => 'Правила очистки кеша постов',
                    'pattern' => 'Паттерн URL для постов',
                    'pattern_comment' => 'Выберите паттерн URL для постов',
                    'pattern_post_slug' => 'Переменная слага поста',
                    'pattern_post_slug_comment' => 'Напишите имя переменной слага поста (пр: slug)',
                    'pattern_category_slug' => 'Переменная слага категории',
                    'pattern_category_slug_comment' => 'Напишите имя переменной слага категории (пр: slug)',
                    'extra_urls' => 'Дополнительнык Url-ы',
                    'extra_urls_comment' => 'Url-ы которые нужно очистить после сохранения поста. Для рекурсивной очистки добавте "*" в конце (пр: /blog/* , при этом url /blog не очистится, нужно добавить оба варианта отдельно)',
                    'extra_urls_prompt' => 'Добавить url',
                    'extra_urls_label' => 'Url',
                ],
                'category' => [
                    'label' => 'Категория',
                    'comment' => 'Правила очистки кеша категорий',
                    'pattern' => 'Паттерн URL для категорий',
                    'pattern_comment' => 'Выберите паттерн URL для категорий',
                    'pattern_slug' => 'Переменная слага категории',
                    'pattern_slug_comment' => 'Напишите имя переменной слага категории (пр: slug)',
                    'extra_urls' => 'Дополнительные Urls',
                    'extra_urls_comment' => 'Url-ы которые нужно очистить после сохранения категории. Для рекурсивной очистки добавте "*" в конце (пр: /blog/* , при этом url /blog не очистится, нужно добавить оба варианта отдельно)',
                    'extra_urls_prompt' => 'Добавить url',
                    'extra_urls_label' => 'Url',
                ]
            ]
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
