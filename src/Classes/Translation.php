<?php

	namespace WeDevelop4You\TranslationFinder\Classes;

	use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Filesystem\Filesystem;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\ExistingTranslationKeyException;
    use WeDevelop4You\TranslationFinder\Exceptions\ParameterRequiredException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedLocaleException;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;

    class Translation
	{
        /**
         * Creates a new translation key
         *
         * @param string $key
         * @param string $group
         * @param string|null $environment
         * @return TranslationKey
         * @throws ParameterRequiredException|EnvironmentNotFoundException|ExistingTranslationKeyException
         */
        public function create(string $key, string $group = '_json', ?string $environment = null): TranslationKey
        {
            $environment = $this->createValidEnvironment($environment);

            if ($this->doesNotExist($environment, $group, $key)) {
                $translationKey = new TranslationKey();
                $translationKey->environment = $environment;
                $translationKey->group = $group;
                $translationKey->key = $key;
                $translationKey->save();

                return $translationKey;
            } else {
                throw new ExistingTranslationKeyException("The translation key with [{$environment}] as environment, [{$group}] as group and [{$key}] as key already exists");
            }
        }

        /**
         * Adds or updates a translation to a translation key
         *
         * @param TranslationKey $translationKey
         * @param string $text
         * @param string|null $locale
         * @return bool
         * @throws UnsupportedLocaleException
         * @throws FileNotFoundException
         */
        public function addOrUpdate(TranslationKey $translationKey, string $text, ?string $locale = null): bool
        {
            if (is_null($locale)) {
                $locale = Config::getDefaultLocale();
            }

            $locales = (new Filesystem)->getRequire(__DIR__.'../../locales.php');

            if (!in_array($locale, $locales)) {
                throw new UnsupportedLocaleException("This [{$locale}] locale is not a valid locale");
            }

            $translation = $translationKey->translations()->where('locale', $locale)->firstOrNew();
            $translation->locale = $locale;
            $translation->translation = $text;

            return $translation->save();
        }

        /**
         * Creates a new translation key and adds a translation to it
         *
         * @param string $key
         * @param string $text
         * @param string $group
         * @param string|null $environment
         * @param string|null $locale
         * @return TranslationKey
         * @throws EnvironmentNotFoundException|ExistingTranslationKeyException|ParameterRequiredException|UnsupportedLocaleException|FileNotFoundException
         */
        public function createAndAdd(string $key, string $text, string $group = '_json', ?string $environment = null, ?string $locale = null): TranslationKey
        {
            $translationKey = $this->create($key, $group, $environment);
            $this->addOrUpdate($translationKey, $text, $locale);

            return $translationKey;
        }

        /**
         * Checks if the translation key doesn't exist anymore in the hole project and then deletes it
         *
         * @param TranslationKey $key
         */
        public function delete(TranslationKey $key)
        {
            //TODO delete key only when the key is never found in the project
        }

        /**
         * Deletes the translation key
         *
         * @param TranslationKey $key
         * @return bool
         */
        public function forceDelete(TranslationKey $key): bool
        {
            return $key->delete();
        }

        /**
         * Checks if the translation key exists
         *
         * @param string $key
         * @param string $group
         * @param string|null $environment
         * @return bool
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        public function exists(string $key, string $group = '_json', ?string $environment = null): bool
        {
            $environment = $this->createValidEnvironment($environment);

            return TranslationKey::where('environment', $environment)->where('group', $group)->where('key', $key)->exists();
        }

        /**
         * Checks if the translation key doesn't exist
         *
         * @param string $key
         * @param string $group
         * @param string|null $environment
         * @return bool
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        public function doesNotExist(string $key, string $group = '_json', ?string $environment = null): bool
        {
            return !$this->exists($environment, $group, $key);
        }

        /**
         * Gets the environment and checks if the environment is valid
         *
         * @param string|null $environment
         * @return string
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        private function createValidEnvironment(?string $environment = null): string
        {
            if (is_null($environment)) {
                if (Config::isEnvironmentsSeparated()) {
                    throw new ParameterRequiredException("The [environment] parameter is required when using separated environments.");
                } else {
                    $environment = Config::DEFAULT_ENVIRONMENT;
                }
            } else if (!in_array($environment, Config::getEnvironments()->toArray())) {
                throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
            }

            return $environment;
        }
	}
