<?php

    namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\ClassNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
    use WeDevelop4You\TranslationFinder\Resource\Config\Database;
    use WeDevelop4You\TranslationFinder\Resource\Config\Environment;
    use WeDevelop4You\TranslationFinder\Resource\Config\Functions;
    use WeDevelop4You\TranslationFinder\Resource\Config\Packages;

    /**
     * Class ConfigBuilder
     * @package WeDevelop4You\Translation\Classes
     *
     * @property-read Database database
     * @property-read Packages packages
     * @property-read Functions functions
     * @property-read string defaultLocale
     * @property-read bool useTranslationSource
     * @property-read Collection|Environment[] $environments
     */
    class Config
	{
        public const DEFAULT_ENVIRONMENT = 'default';

        /**
         * ConfigBuilder constructor.
         *
         * @throws ClassNotFoundException|EnvironmentNotFoundException|MethodNotCallableException|UnsupportedFileExtensionException
         */
        public function __construct()
        {
            $this->setPackages();
            $this->setDatabase();
            $this->setFunctions();
            $this->setProperties();
            $this->setEnvironments();
        }

        /**
         * Build th hole config
         *
         * @return Config
         */
        public static function build(): Config
        {
            return new static();
        }

        /**
         * Gets all environment names
         *
         * @return Collection
         */
        public static function getEnvironments(): Collection
        {
            return collect(config('translation.environment.options', []))->keys();
        }

        /**
         * Gets the default locale
         *
         * @return string
         */
        public static function getDefaultLocale(): string
        {
            return config('translation.default_locale');
        }

        /**
         * Checks if separated environments is used
         *
         * @return bool
         */
        public static function isEnvironmentsSeparated(): bool
        {
            return config('translation.environment.separate_environment');
        }

        /**
         * Creates all properties for the config
         */
        private function setProperties(): void
        {
            $this->defaultLocale = $this->getDefaultLocale();
            $this->useTranslationSource = config('translation.use_translation_source');
        }

        /**
         * Gets all functions from the class
         *
         * @throws MethodNotCallableException|ClassNotFoundException
         */
        private function setFunctions(): void
        {
            $this->functions = new Functions();
        }

        /**
         * Creates all environments for the config
         *
         * @throws EnvironmentNotFoundException|UnsupportedFileExtensionException
         */
        private function setEnvironments(): void
        {
            $this->environments = new Collection();
            $allEnvironments = $this->getEnvironments()->flip();

            $environments = self::isEnvironmentsSeparated()
                ? $allEnvironments->except(self::DEFAULT_ENVIRONMENT)
                : $allEnvironments->only(self::DEFAULT_ENVIRONMENT);

            $environments->flip()->each(function ($environment) {
                $this->environments->push(new Environment($environment));
            });
        }

        /**
         * Creates database config
         *
         * @throws EnvironmentNotFoundException
         */
        private function setDatabase(): void
        {
            $config = (object) config('translation.database');

            $this->database = new Database($config);
        }

        /**
         * @throws EnvironmentNotFoundException
         */
        private function setPackages(): void
        {
            $config = (object) config('translation.packages');

            $this->packages = new Packages($config);
        }

        /**
         * @param $name
         * @return mixed
         */
        public function __get($name)
        {
            return $this->$name;
        }
    }
