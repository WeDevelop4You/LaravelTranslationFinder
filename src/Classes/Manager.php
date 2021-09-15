<?php


	namespace WeDevelop4You\TranslationFinder\Classes;

    use Exception;
    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Classes\Find\TranslationFinder;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToBuildTranslationFileException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToSearchTranslationsException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
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
                $searcher = new TranslationFinder();

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
	}
