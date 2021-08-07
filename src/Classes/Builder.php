<?php


	namespace WeDevelop4You\TranslationFinder\Classes;


	use Illuminate\Support\Arr;
    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;
    use WeDevelop4You\TranslationFinder\Resource\Config\Storage;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
    use WeDevelop4You\TranslationFinder\Models\Translation;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;

    class Builder
	{
        /**
         * @var Config
         */
        private Config $config;

        /**
         * @var array
         */
        private array $rebuildFiles = [];

        /**
         * @var bool
         */
        private bool $filesNeedsToRebuild;

        /**
         * Builder constructor.
         * @param bool $filesNeedsToRebuild
         */
        public function __construct(bool $filesNeedsToRebuild = false)
        {
            $this->config = Config::build();
            $this->filesNeedsToRebuild = $filesNeedsToRebuild;
        }

        /**
         * @param TranslationKey $translationKey
         * @throws EnvironmentNotFoundException
         * @throws UnsupportedFileExtensionException
         * @throws MethodNotCallableException
         */
        public function addTranslationToFile(TranslationKey $translationKey): void
        {
            $translationKey->translations->each(function (Translation $translation) use ($translationKey) {
                $storageConfig = $this->getEnvironmentConfig($translationKey->environment);
                $fullPath = $this->createFullPath($translationKey, $storageConfig, $translation->locale);

                (!$this->filesNeedsToRebuild || in_array($fullPath, $this->rebuildFiles))
                    ? $translations = call_user_func($this->config->getFileData, $fullPath)
                    : $this->rebuildFiles[] = $fullPath;


                Str::startsWith($translationKey->group, '_')
                    ? $translations[$translationKey->key] = $translation->translation
                    : Arr::set($translations, $translationKey->key, $translation->translation);

                $output = call_user_func($this->config->buildFile, $fullPath, $translations);

                file_put_contents($fullPath, $output);
            });
        }

        /**
         * @param string $environment
         * @return Storage
         * @throws EnvironmentNotFoundException
         */
        private function getEnvironmentConfig(string $environment): Storage
        {
            $environmentConfig = $this->config->environments->firstWhere('name', $environment);

            if (is_null($environmentConfig)) {
                throw new EnvironmentNotFoundException("Environment [{$environment}] doesn't exist in the translation config file");
            }

            return $environmentConfig->storage;
        }

        /**
         * @param TranslationKey $translationKey
         * @param Storage $storageConfig
         * @param string $locale
         * @return string
         */
        private function createFullPath(TranslationKey $translationKey, Storage $storageConfig, string $locale): string
        {
            $path = $storageConfig->path;
            $extension = $storageConfig->extension;

            if ($translationKey->group == '_json') {
                $direction = $path;
                $fullPath = "{$direction}/$locale.json";
            } else {
                $direction = "{$path}/{$locale}";
                $fullPath = "{$direction}/{$translationKey->group}.{$extension}";
            }

            if (!is_dir($direction)) {
                mkdir($direction);
                chmod($direction, 0777);
            }

            return $fullPath;
        }
	}
