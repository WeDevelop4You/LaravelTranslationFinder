<?php


	namespace WeDevelop4You\TranslationFinder\Classes;

    use Exception;
    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Classes\Find\Search;
    use WeDevelop4You\TranslationFinder\Classes\Store\Builder;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\ExistingTranslationKeyException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToBuildTranslationFileException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToSearchTranslationsException;
    use WeDevelop4You\TranslationFinder\Exceptions\ParameterRequiredException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedLocaleException;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;

    class Manager
	{
        /**
         * Search to the hole project for translation keys
         *
         * @return Collection
         * @throws FailedToSearchTranslationsException
         */
        public static function search(): Collection
        {
            try {
                $searcher = new Search();

                return $searcher->saveCounter;
            } catch (Exception $e) {
                throw new FailedToSearchTranslationsException($e->getMessage());
            }
        }

        /**
         * publish a specific translation
         *
         * @param TranslationKey $translationKey
         * @return bool
         * @throws FailedToBuildTranslationFileException
         */
        public static function publish(TranslationKey $translationKey): bool
        {
            try {
                $builder = new Builder();
                $builder->addTranslationToFile($translationKey);

                return true;
            } catch (EnvironmentNotFoundException | UnsupportedFileExtensionException $e) {
                throw new FailedToBuildTranslationFileException($e->getMessage());
            }
        }

        /**
         * Reset all files and publish all translations
         *
         * @return bool
         * @throws FailedToBuildTranslationFileException
         */
        public static function publishAll(): bool
        {
            try {
                $builder = new Builder(true);

                TranslationKey::all()->each(function(TranslationKey $translationKey) use ($builder) {
                    $builder->addTranslationToFile($translationKey);
                });

                return true;
            } catch (EnvironmentNotFoundException | UnsupportedFileExtensionException $e) {
                throw new FailedToBuildTranslationFileException($e->getMessage());
            }
        }

        /**
         * @return Translation
         */
        public static function translation(): Translation
        {
            return new Translation();
        }

        /**
         * Adds or updates a translation and then publish the translation
         *
         * @param TranslationKey $translationKey
         * @param string $text
         * @param string|null $locale
         * @return bool
         * @throws FailedToBuildTranslationFileException|UnsupportedLocaleException|FileNotFoundException
         */
        public static function addOrUpdateAndPublish(TranslationKey $translationKey, string $text, ?string $locale = null): bool
        {
            self::translation()->addOrUpdate($translationKey, $text, $locale);
            return self::publish($translationKey);
        }

        /**
         * Creates a new translation key, adds a translation to it and then publish the translation
         *
         * @param string $key
         * @param string $text
         * @param string $group
         * @param string|null $environment
         * @param string|null $locale
         * @return bool
         * @throws EnvironmentNotFoundException|FailedToBuildTranslationFileException|UnsupportedLocaleException|ExistingTranslationKeyException|ParameterRequiredException
         */
        public static function createAddAndPublish(string $key, string $text, string $group = '_json', ?string $environment = null, ?string $locale = null): bool
        {
            $translationKey = self::translation()->createAndAdd($key, $text, $group, $environment, $locale);
            return self::publish($translationKey);
        }
	}
