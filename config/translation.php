<?php

    return [
        'use_translation_source' => true,

        'environment' => [
            'separate_environment' => false,

            'options' => [
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
                ]
            ],
        ],

        'key_class' => \WeDevelop4You\TranslationFinder\Classes\Key::class,

        'file_class' => \WeDevelop4You\TranslationFinder\Classes\File::class,
    ];
