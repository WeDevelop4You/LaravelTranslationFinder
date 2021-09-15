<?php

    return [
        'default_locale' => config('app.fallback_locale'),

        'environment' => [
            'separate_environment' => false,

            'options' => [
                'default' => [
                    'finder' => [
                        'path' => base_path(),
                        'extension' => '*.php',
                        'exclude_paths' => [],
                        'functions' => [
                            '__',
                            'trans',
                            'trans_choice'
                        ],
                        'ignore_groups' => [
                            'auth'
                        ],
                        'tag' => 'project',
                    ],

                    'storage' => [
                        'path' => resource_path('lang'),
                        'extension' => '.php',
                    ],
                ],

                'backend' => [
                    'finder' => [
                        'path' => app_path(),
                        'extension' => '*.php',
                        'exclude_paths' => [],
                        'functions' => [
                            '__',
                            'trans',
                            'trans_choice'
                        ],
                        'ignore_groups' => [
                            'auth'
                        ],
                        'tag' => 'project',
                    ],

                    'storage' => [
                        'path' => resource_path('lang'),
                        'extension' => '.php',
                    ],
                ],

                'frontend' => [
                    'finder' => [
                        'path' => resource_path('js'),
                        'extension' => '*.vue',
                        'exclude_paths' => [],
                        'functions' => [
                            't',
                        ],
                        'ignore_groups' => [],
                        'tag' => 'project',
                    ],

                    'storage' => [
                        'path' => resource_path('js/lang'),
                        'extension' => '.json',
                    ],
                ],
            ],
        ],

        'use_translation_source' => false,

        'database' => [
            'search_models' => false,

            'tag' => 'database',

            'default_environment' => 'default',

            'model_path' => app_path('Models'),
        ],

        'packages' => [
            'get_translations' => false,

            'tag' => 'packages',

            'use_packages_name_tags' => true,

            'default_environment' => 'default',

            'paths' => [],
        ],

        'helpers' => [
            'file_content' => \WeDevelop4You\TranslationFinder\Helpers\FileContentHelper::class,

            'key_separator' => \WeDevelop4You\TranslationFinder\Helpers\KeySeparatorHelper::class,
        ],

    ];
