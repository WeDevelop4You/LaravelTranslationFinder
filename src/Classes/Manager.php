<?php


	namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToBuildTranslationFileException;
    use WeDevelop4You\TranslationFinder\Exceptions\FailedToSearchTranslationsException;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;

    class Manager
	{
        /**
         * @return Collection
         * @throws FailedToSearchTranslationsException
         */
        public static function search(): Collection
        {
            try {
                $searcher = new Search();

                return $searcher->saveCounter;
            } catch (MethodNotCallableException $e) {
                throw new FailedToSearchTranslationsException($e->getMessage());
            }
        }

        /**
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
	}
