<?php

    namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Filesystem\Filesystem;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\ExistingTranslationKeyException;
    use WeDevelop4You\TranslationFinder\Exceptions\ParameterRequiredException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedLocaleException;
    use WeDevelop4You\TranslationFinder\Helpers\ValidateHelper;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;

    class Translation
    {
        /**
         * @var TranslationKey|null
         */
        public TranslationKey $translationKey;

        /**
         * Creates a new translation key.
         *
         * @param string      $key
         * @param string      $group
         * @param string|null $environment
         *
         * @return Translation
         *
         * @throws EnvironmentNotFoundException|ExistingTranslationKeyException|ParameterRequiredException
         */
        public function create(string $key, string $group = Config::DEFAULT_GROUP, ?string $environment = null): Translation
        {
            $environment = ValidateHelper::environment($environment);

            if ($this->doesNotExist($key, $group, $environment)) {
                $translationKey = new TranslationKey();
                $translationKey->environment = $environment;
                $translationKey->group = $group;
                $translationKey->key = $key;
                $translationKey->save();

                $this->translationKey = $translationKey;

                return $this;
            } else {
                throw new ExistingTranslationKeyException("The translation key with [{$environment}] as environment, [{$group}] as group and [{$key}] as key already exists");
            }
        }

        /**
         * Adds or updates a translation to a translation key.
         *
         * @param string      $text
         * @param string|null $locale
         *
         * @return bool
         *
         * @throws UnsupportedLocaleException
         * @throws FileNotFoundException|ParameterRequiredException
         */
        public function addOrUpdate(string $text, ?string $locale = null): bool
        {
            $this->checkIfTranslationKeyIsset();

            if (is_null($locale)) {
                $locale = Config::getDefaultLocale();
            }

            $locales = (new Filesystem())->getRequire(__DIR__.'/../../locales.php');

            if (!in_array($locale, $locales)) {
                throw new UnsupportedLocaleException("This [{$locale}] locale is not a valid locale");
            }

            $translation = $this->translationKey->getOrCreateTranslation($locale);
            $translation->locale = $locale;
            $translation->translation = $text;

            return $translation->save();
        }

        /**
         * @param string $tag
         *
         * @return Translation
         *
         * @throws ParameterRequiredException
         */
        public function addTag(string $tag): Translation
        {
            $this->checkIfTranslationKeyIsset();

            $tags = $this->translationKey->tags;

            return $this->updateTags($tags[$tag]);
        }

        /**
         * @param array|null $tags
         *
         * @return Translation
         *
         * @throws ParameterRequiredException
         */
        public function updateTags(?array $tags): Translation
        {
            $this->checkIfTranslationKeyIsset();

            $this->translationKey->tags = $tags;
            $this->translationKey->save();

            return $this;
        }

        /**
         * Checks if the translation key doesn't exist anymore in the hole project and then deletes it.
         *
         * @throws ParameterRequiredException
         */
        public function delete()
        {
            $this->checkIfTranslationKeyIsset();

            //TODO delete key only when the key is never found in the project
        }

        /**
         * Deletes the translation key.
         *
         * @return bool
         *
         * @throws ParameterRequiredException
         */
        public function forceDelete(): bool
        {
            $this->checkIfTranslationKeyIsset();

            return $this->translationKey->delete();
        }

        /**
         * Checks if the translation key exists.
         *
         * @param string      $key
         * @param string      $group
         * @param string|null $environment
         *
         * @return bool
         *
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        public function exists(string $key, string $group = Config::DEFAULT_GROUP, ?string $environment = null): bool
        {
            $translationKey = TranslationKey::where('environment', ValidateHelper::environment($environment))
                ->where('group', $group)
                ->where('key', $key)
                ->first();

            if (is_null($translationKey)) {
                return false;
            }

            $this->translationKey = $translationKey;

            return true;
        }

        /**
         * Checks if the translation key doesn't exist.
         *
         * @param string      $key
         * @param string      $group
         * @param string|null $environment
         *
         * @return bool
         *
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        public function doesNotExist(string $key, string $group = Config::DEFAULT_GROUP, ?string $environment = null): bool
        {
            return !$this->exists($key, $group, $environment);
        }

        /**
         * @throws ParameterRequiredException
         */
        private function checkIfTranslationKeyIsset()
        {
            if (!isset($this->translationKey)) {
                throw new ParameterRequiredException("Parameter [translationKey] wasn't set");
            }
        }
    }
