<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Find;

	use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Helpers\FileContentHelper;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Resource\Config\Packages;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class PackagesSearcher
	{
        /**
         * @var string
         */
        private string $getFileContent;

	    /**
         * @var Packages
         */
        private Packages $packagesConfig;

        /**
         * @var ProgressBarHelper
         */
        private ProgressBarHelper $progressBar;

        /**
         * @param Packages $packagesConfig
         * @param string $getFileContent
         */
        public function __construct(Packages $packagesConfig, string $getFileContent)
        {
            $this->packagesConfig = $packagesConfig;
            $this->getFileContent = $getFileContent;
        }

        public function find(): Collection
        {
            return $this->packagesConfig->paths->flatMap(function (string $environment, string $path) {
                $content = call_user_func($this->getFileContent, base_path($path));

                if (App::runningInConsole()) {
                    $this->progressBar = new ProgressBarHelper("Searching packages translations in: {$path}", count($content, COUNT_RECURSIVE));
                }

                $packagesTranslations = $this->create($content, $path, $environment);

                if (isset($this->progressBar)) {
                    $this->progressBar->finish();
                }

                return $packagesTranslations;
            });
        }

        /**
         * @param array $content
         * @param string $path
         * @param string $environment
         * @param string|null $extendKey
         * @return array
         */
        private function create(array $content, string $path, string $environment, ?string $extendKey = null): array
        {
            $translations = [];

            $group = pathinfo($path, PATHINFO_EXTENSION) === FileContentHelper::FILE_EXTENSION_json
                ? Config::DEFAULT_GROUP
                : pathinfo($path, PATHINFO_FILENAME);

            $pathLevelBack = $group === Config::DEFAULT_GROUP ? 1 : 2;

            $packagesName = basename(dirname($path, $pathLevelBack));

            foreach ($content as $index => $value) {
                $key = is_null($extendKey)
                    ? $index
                    : "{$extendKey}.{$index}";

                if (is_array($value)) {
                    $dotTranslations = $this->create($value, $path, $environment, $key);

                    $translations = array_merge($translations, $dotTranslations);
                } else {
                    $tags[] = $this->packagesConfig->tag;

                    if ($this->packagesConfig->usePackagesNameTags) {
                        $tags[] = $packagesName;
                    }

                    $translation = new TranslationResource();
                    $translation->environment = $environment;
                    $translation->group = $group;
                    $translation->value = $value;
                    $translation->path = $path;
                    $translation->key = $key;

                    $translation->setTags($tags);

                    $translations[] = $translation;

                    if (isset($this->progressBar)) {
                        $this->progressBar->add();
                    }

                    unset($tags);
                }
            }

            return $translations;
        }
    }
