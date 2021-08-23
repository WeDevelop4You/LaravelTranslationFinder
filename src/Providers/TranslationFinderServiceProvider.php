<?php

    namespace WeDevelop4You\TranslationFinder\Providers;


    use Illuminate\Support\ServiceProvider;
    use WeDevelop4You\TranslationFinder\Console\Commands\{
        FindTranslations,
        ResetTranslations,
        PublishTranslations,
        DiscoverTranslationModels,
    };

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
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
            $this->publishes([__DIR__.'/../../config/translation.php' => config_path('translation.php')], 'translation-finder');

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
