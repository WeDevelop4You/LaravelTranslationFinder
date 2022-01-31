<?php

    namespace WeDevelop4You\TranslationFinder\Classes\Find;

    use Exception;
    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Classes\Bootstrap\TranslationPackagesPath;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Classes\Translation;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\ExistingTranslationKeyException;
    use WeDevelop4You\TranslationFinder\Exceptions\ParameterRequiredException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedLocaleException;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Helpers\TruncateTableHelper;
    use WeDevelop4You\TranslationFinder\Models\TranslationSource;
    use WeDevelop4You\TranslationFinder\Resource\Config\Environment;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class TranslationFinder
    {
        /**
         * @var Collection|TranslationResource[]
         */
        private $translations;

        /**
         * @var Config
         */
        private Config $config;

        /**
         * @var Collection
         */
        public Collection $saveCounter;

        /**
         * @var TranslationPackagesPath
         */
        private TranslationPackagesPath $packagesPath;

        /**
         * Search constructor.
         *
         * @throws Exception
         */
        public function __construct()
        {
            $this->config = Config::build();
            $this->translations = new Collection();

            $this->searchInProject();

            if ($this->config->database->searchModels) {
                $this->searchInDatabase();
            }

            if ($this->config->packages->getTranslations) {
                $this->searchInPackages();
            }

            $this->save();
        }

        private function searchInProject(): void
        {
            $this->saveCounter = new Collection();

            $this->config->environments->each(function (Environment $environment) {
                $this->saveCounter->put($environment->name, 0);

                $project = new ProjectSearcher($environment->finder, $this->config->functions->default, $environment->name);
                $project->find()->each(function (TranslationResource $translationResource) {
                    $this->removeDuplicates($translationResource);
                });
            });
        }

        /**
         * @throws Exception
         */
        private function searchInDatabase(): void
        {
            $config = $this->config;

            $database = new DatabaseSearcher($config->database, $config->functions->database);
            $database->find()->each(function (TranslationResource $translationResource) {
                $this->removeDuplicates($translationResource);
            });
        }

        private function searchInPackages(): void
        {
            $config = $this->config;

            $this->packagesPath = new TranslationPackagesPath();
            $this->packagesPath->reset();

            $packages = new PackagesSearcher($config->packages, $this->config->functions->get, );
            $packages->find()->each(function (TranslationResource $translationResource) {
                $this->removeDuplicates($translationResource);
            });
        }

        /**
         * @param TranslationResource $translationResource
         */
        private function removeDuplicates(TranslationResource $translationResource): void
        {
            $translation = $this->translations
                ->where('environment', $translationResource->environment)
                ->where('group', $translationResource->group)
                ->where('key', $translationResource->key)
                ->first();

            if (is_null($translation)) {
                $this->translations->push($translationResource);
            } else {
                $translation->setTags($translationResource->getTags());

                if (isset($translationResource->value)) {
                    $translation->value = $translationResource->value;
                }

                if (isset($translationResource->path)) {
                    $translation->path = $translationResource->path;
                }
            }
        }

        /**
         * @throws EnvironmentNotFoundException|Exception|ExistingTranslationKeyException|FileNotFoundException|ParameterRequiredException|UnsupportedLocaleException
         */
        private function save(): void
        {
            TruncateTableHelper::sources();

            $translations = $this->translations;

            if (App::runningInConsole()) {
                $progressBar = new ProgressBarHelper("Save translations in database", $translations->count());
            } else {
                $progressBar = null;
            }

            $translations->each(function (TranslationResource $translationResource) use ($progressBar) {
                $translation = new Translation();

                $key = $translationResource->key;
                $group = $translationResource->group;
                $environment = $translationResource->environment;

                if ($translation->doesNotExist($key, $group, $environment)) {
                    $translation->create($key, $group, $environment);

                    $this->saveCounter[$environment] += 1;
                }

                $translation->updateTags($translationResource->getTags());

                if (Str::startsWith($group, '_') || isset($translationResource->value)) {
                    $translation->addOrUpdate($translationResource->value ?? $key);
                }

                if ($this->config->useTranslationSource) {
                    $translationResource->sources->each(function (string $source) use ($translation) {
                        $translationSource = new TranslationSource();
                        $translationSource->source = $source;

                        $translation->translationKey->sources()->save($translationSource);
                    });
                }

                if (isset($translationResource->path) && isset($this->packagesPath)) {
                    $id = $translation->translationKey->id;
                    $path = $translationResource->path;

                    $this->packagesPath->add($id, $path);
                }

                if (isset($progressBar)) {
                    $progressBar->add();
                }
            });

            if (isset($progressBar)) {
                $progressBar->finish();
            }
        }
    }
