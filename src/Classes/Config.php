<?php

    namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Schema;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\FileDoesNotExistException;
    use WeDevelop4You\TranslationFinder\Exceptions\SettingNotAllowedException;
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
        public const DEFAULT_GROUP = '_json';

        public const DEFAULT_ENVIRONMENT = 'default';

        private const SOURCE_TABLE = 'translation_sources';

        /**
         * ConfigBuilder constructor.
         *
         * @throws EnvironmentNotFoundException|UnsupportedFileExtensionException|SettingNotAllowedException|FileDoesNotExistException
         */
        public function __construct()
        {
            $this->setPackages();
            $this->setDatabase();
            $this->setFunctions();
            $this->setProperties();
            $this->setEnvironments();
            $this->checkIfTableExists();
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
         * @return bool
         */
        public static function isTranslationsSourceUsed(): bool
        {
            return config('translation.use_translation_source');
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
            $this->useTranslationSource = self::isTranslationsSourceUsed();
        }

        /**
         * Gets all functions from the class
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
         * @throws EnvironmentNotFoundException|FileDoesNotExistException
         */
        private function setPackages(): void
        {
            $config = (object) config('translation.packages');

            $this->packages = new Packages($config);
        }

        /**
         * @throws SettingNotAllowedException
         */
        private function checkIfTableExists()
        {
            if ($this->useTranslationSource && !Schema::hasTable(self::SOURCE_TABLE)) {
                throw (new SettingNotAllowedException())->tableDoesNotExist(self::SOURCE_TABLE);
            }
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
