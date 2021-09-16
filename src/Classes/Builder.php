<?php

    namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Classes\Bootstrap\TranslationPackagesPath;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Helpers\FileContentHelper;
    use WeDevelop4You\TranslationFinder\Models\Translation;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;
    use WeDevelop4You\TranslationFinder\Resource\Config\Storage;

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
         * @var TranslationPackagesPath
         */
        private TranslationPackagesPath $packagesPath;

        /**
         * Builder constructor.
         * @param bool $filesNeedsToRebuild
         */
        public function __construct(bool $filesNeedsToRebuild = false)
        {
            $this->config = Config::build();
            $this->filesNeedsToRebuild = $filesNeedsToRebuild;

            if ($this->config->packages->getTranslations) {
                $this->packagesPath = new TranslationPackagesPath();
            }
        }

        /**
         * @param TranslationKey $translationKey
         * @throws EnvironmentNotFoundException|FileNotFoundException
         */
        public function addTranslationToFile(TranslationKey $translationKey): void
        {
            $file = new Filesystem();

            $translationKey->translations->each(function (Translation $translation) use ($translationKey, $file) {
                $fullPath = $this->createFullPath($translationKey, $translation->locale);

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
         * @param TranslationKey $translationKey
         * @param string $locale
         * @return string
         * @throws EnvironmentNotFoundException|FileNotFoundException
         */
        private function createFullPath(TranslationKey $translationKey, string $locale): string
        {
            $group = $translationKey->group;

            if (isset($this->packagesPath)) {
                $packagesPath = $this->packagesPath->has($translationKey->id);
            }

            if (isset($packagesPath) && $packagesPath) {
                $pathLevelBack = $group === Config::DEFAULT_GROUP ? 1 : 2;

                $path = dirname($packagesPath, $pathLevelBack);
                $extension = pathinfo($packagesPath, PATHINFO_EXTENSION);
            } else {
                $storageConfig = $this->getEnvironmentConfig($translationKey->environment);

                $path = $storageConfig->path;
                $extension = $storageConfig->extension;
            }

            if ($group === Config::DEFAULT_GROUP) {
                $extension = FileContentHelper::FILE_EXTENSION_json;

                $direction = $path;
                $fullPath = "{$direction}/{$locale}.{$extension}";
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
