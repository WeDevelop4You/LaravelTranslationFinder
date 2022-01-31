<?php

    namespace WeDevelop4You\TranslationFinder\Providers;

    use Illuminate\Support\ServiceProvider;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Console\Commands\DiscoverTranslationModels;
    use WeDevelop4You\TranslationFinder\Console\Commands\FindTranslations;
    use WeDevelop4You\TranslationFinder\Console\Commands\PublishTranslations;
    use WeDevelop4You\TranslationFinder\Console\Commands\ResetTranslations;

    class TranslationFinderServiceProvider extends ServiceProvider
    {
        /**
         * Register services.
         *
         * @return void
         */
        public function register()
        {
            $this->mergeConfigFrom(__DIR__.'/../../config/translation.php', 'translation');
        }

        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot()
        {
            $this->LoadMigrations();
            $this->loadArtisanCommands();
            $this->publishes([__DIR__.'/../../config/translation.php' => config_path('translation.php')], 'translation-finder');
        }

        private function LoadMigrations()
        {
            $this->loadMigrationsFrom([
                __DIR__.'/../../database/migrations/2021_07_21_000001_create_translation_keys_table.php',
                __DIR__.'/../../database/migrations/2021_07_21_000002_create_translations_table.php',
            ]);

            if (Config::isTranslationsSourceUsed()) {
                $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/2021_07_21_000003_create_translation_sources_table.php');
            }
        }

        private function loadArtisanCommands()
        {
            if ($this->app->runningInConsole()) {
                $this->commands([
                    FindTranslations::class,
                    ResetTranslations::class,
                    PublishTranslations::class,
                    DiscoverTranslationModels::class,
                ]);
            }
        }
    }
