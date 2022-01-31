<?php

    namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Arr;
    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\FileDoesNotExistException;
    use WeDevelop4You\TranslationFinder\Helpers\ValidateHelper;

    /**
     * Class Packages.
     *
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Database
     *
     * @property-read array tags
     * @property-read Collection $paths
     * @property-read bool getTranslations
     * @property-read bool usePackagesNameTags
     */
    class Packages
    {
        /**
         * Packages constructor.
         *
         * @param object $config
         *
         * @throws EnvironmentNotFoundException|FileDoesNotExistException
         */
        public function __construct(object $config)
        {
            $this->tags = Arr::wrap($config->tags);
            $this->getTranslations = $config->use_packages;
            $this->usePackagesNameTags = $config->use_packages_name_tags;
            $this->paths = ValidateHelper::environments($config->paths, $config->default_environment);

            $this->checkIfFileExists();
        }

        /**
         * @throws FileDoesNotExistException
         */
        private function checkIfFileExists(): void
        {
            foreach ($this->paths as $path => $environment) {
                $fullPath = base_path($path);

                if (!file_exists($fullPath)) {
                    throw new FileDoesNotExistException("File [{$fullPath}] doesn't exist'");
                }
            }
        }
    }
