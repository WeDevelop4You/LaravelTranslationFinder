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
                    ],

                    'storage' => [
                        'path' => resource_path('js/lang'),
                        'extension' => '.json',
                    ],
                ],
            ],
        ],

        'use_translation_source' => true,

        'database' => [
            'search_models' => true,

            'default_environment' => 'default',

            'model_path' => app_path('Models'),
        ],

        'packages' => [
            'get_translations' => true,

            'default_environment' => 'default',

            'paths' => [
                'resources/lang/en/auth.php' => 'backend',
                'resources/lang/en/pagination.php',
                'resources/lang/en/password.php',
                'resources/lang/en/validation.php',
            ],
        ],

        'classes' => [
            'file' => \WeDevelop4You\TranslationFinder\Classes\Store\File::class,

            'separator' => \WeDevelop4You\TranslationFinder\Classes\Find\Separator::class,
        ],

    ];
