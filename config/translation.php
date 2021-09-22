<?php

    return [
        /*
         * The "default_locale" is the languages where the translations are saved in. when
         * it searches for translations and finds a string key and not a dot key, then the
         * translation will also be saved and not just the key.
         *
         * Example of the keys:
         *
         * String key:  trans('hello this is a string key')
         * Dot key:     trans('auth.password')
         *
         * The string key is also your translation so not only the key will be saved also
         * the translation. In the languages you specified in "default_locale".
         */
        'default_locale' => config('app.fallback_locale'),

        /*
         * You can separate your environments if you want. If you separate your environments
         * you can make as many as you want for example your "backend" and "frontend". You can
         * adjust all environments how you want it to be. If you don't want them to be separate
         * it will use the "default".
         */
        'environment' => [

            /*
             * separate environments. If not only "default" will be used, if "separate_environment"
             * is used all environments will be used excepted the "default".
             */
            'separate_environment' => false,

            /*
             * When translation are found in a file you can store the path and line
             * where the translation key is found.
             */
            'use_translation_source' => false,

            /*
             * All the set environments
             */
            'options' => [

                /*
                 * The name of the environment, will also be used when stored in the database.
                 */
                'default' => [

                    /*
                     * All the options where to find the translations.
                     *
                     * "path": Where it will search for translations
                     * "extension": In which file extension it will search
                     * "exclude_path": Paths where it doesn't search for translations. Default:
                     *      - vendor
                     *      - storage/framework/views
                     *
                     * "functions": Where the translation key is define in
                     * "ignore_groups": When using dot keys the first word is the group. For example:
                     *      You ignore the group "auth"
                     *
                     *      trans('auth.password')
                     *      trans('auth.your password is wrong')
                     *      trans('validation.current_password')
                     *
                     *      The key "auth.password" and "auth.your password is wrong" won't be
                     *      stored in the database. But key "validation.current_password" will
                     *      be stored
                     *
                     * "tag": When the translation is found it wil be stored in the database
                     *  with a tag where the translation key is found
                     */
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

                    /*
                     * All the options where to store the translations.
                     *
                     * "path": Where all translation file need to be stored
                     * "extension": The extension in which the translation file be stored. String
                     *  Keys will be automatic stored in json, except string keys with start with
                     *  a group. For example:
                     *      trans('hello this is a string key') -> Will be stored in json
                     *      trans('_category.this is a category') -> Will not be stored in json but in the "extension"
                     */
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

        /*
         *
         */
        'database' => [
            'search_models' => false,

            'tag' => 'database',

            'default_environment' => 'default',

            'model_path' => app_path('Models'),
        ],

        /*
         *
         */
        'packages' => [
            'get_translations' => false,

            'tag' => 'packages',

            'use_packages_name_tags' => true,

            'default_environment' => 'default',

            'paths' => [],
        ],

        /*
         * Change the helper classes if it doesn't work for your configuration or submit to
         * the GitHub if you have a new file extension that you want in the packages.
         */
        'helpers' => [
            /*
             * When getting content from a file or putting content in a file it is different for every
             * extension. If your extension doesn't exist in the list you can just extend it or make
             * the existing extension different to your needs.
             *
             * If you want to submit a new extension that others also can use too. Just make a pull
             * request on GitHub. https://github.com/WeDevelop4You/LaravelTranslationFinder/pulls
             */
            'file_content' => \WeDevelop4You\TranslationFinder\Helpers\FileContentHelper::class,

            /*
             * For every environment is the translation key different and needs to be separated
             * differently. You can extend it or make your onw version.
             */
            'key_separator' => \WeDevelop4You\TranslationFinder\Helpers\KeySeparatorHelper::class,
        ],

    ];
