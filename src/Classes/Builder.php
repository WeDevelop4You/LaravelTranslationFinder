<?php


	namespace WeDevelop4You\TranslationFinder\Classes;


	use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Classes\Config;
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
         * @throws EnvironmentNotFoundException | UnsupportedFileExtensionException
         */
        public function addTranslationToFile(TranslationKey $translationKey): void
        {
            $file = new Filesystem();

            $translationKey->translations->each(function (Translation $translation) use ($translationKey, $file) {
                $storageConfig = $this->getEnvironmentConfig($translationKey->environment);
                $fullPath = $this->createFullPath($translationKey->group, $translation->locale, $storageConfig);

                (!$this->filesNeedsToRebuild || in_array($fullPath, $this->rebuildFiles))
                    ? $translations = call_user_func($this->config->functions->get, $fullPath)
                    : $this->rebuildFiles[] = $fullPath;

                Str::startsWith($translationKey->group, '_')
                    ? $translations[$translationKey->key] = $translation->translation
                    : Arr::set($translations, $translationKey->key, $translation->translation);

                $output = call_user_func($this->config->functions->set, $fullPath, $translations);

                $file->put($fullPath, $output);
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
                throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
            }

            return $environmentConfig->storage;
        }

        /**
         * @param string $group
         * @param string $locale
         * @param Storage $storageConfig
         * @return string
         */
        private function createFullPath(string $group, string $locale, Storage $storageConfig): string
        {
            $path = $storageConfig->path;
            $extension = $storageConfig->extension;

            if ($group == '_json') {
                $direction = $path;
                $fullPath = "{$direction}/$locale.json";
            } else {
                $direction = "{$path}/{$locale}";
                $fullPath = "{$direction}/{$group}.{$extension}";
            }

            if (!is_dir($direction)) {
                mkdir($direction);
                chmod($direction, 0777);
            }

            return $fullPath;
        }
	}
