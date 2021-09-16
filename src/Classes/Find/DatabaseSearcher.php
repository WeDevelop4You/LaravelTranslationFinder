<?php

    namespace WeDevelop4You\TranslationFinder\Classes\Find;

    use Exception;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use WeDevelop4You\TranslationFinder\Classes\Bootstrap\TranslationInterfaceFinder;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Helpers\ValidateHelper;
    use WeDevelop4You\TranslationFinder\Resource\Config\Database;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class DatabaseSearcher
    {
        /**
         * @var Collection
         */
        private Collection $modelClasses;

        /**
         * @var Database
         */
        private Database $databaseConfig;

        /**
         * @var string
         */
        private string $databaseKeySeparator;

        /**
         * Search constructor.
         * @throws Exception
         */
        public function __construct(Database $databaseConfig, string $databaseKeySeparator)
        {
            $this->databaseConfig = $databaseConfig;
            $this->databaseKeySeparator = $databaseKeySeparator;
            $this->modelClasses = (new TranslationInterfaceFinder())->get(true);
        }

        /**
         * @return Collection
         */
        public function find(): Collection
        {
            return $this->modelClasses->flatMap(function ($modelClass) {
                $columnEnvironment = $this->getTranslationEnvironments(new $modelClass());

                if (App::runningInConsole()) {
                    $progressBar = new ProgressBarHelper("Searching database rows in model: {$modelClass}", $modelClass::count());
                } else {
                    $progressBar = null;
                }

                if (!is_null($columnEnvironment)) {
                    $columns = $columnEnvironment->keys()->toArray();

                    $modelTranslations = $modelClass::all($columns)->flatMap(function ($data) use ($columnEnvironment, $progressBar) {
                        if (isset($progressBar)) {
                            $progressBar->add();
                        }

                        return $this->create($data->getAttributes(), $columnEnvironment);
                    });
                }

                if (isset($progressBar)) {
                    $progressBar->finish();
                }

                return $modelTranslations ?? [];
            });
        }

        /**
         * @param object $model
         * @return Collection
         * @throws EnvironmentNotFoundException
         */
        private function getTranslationEnvironments(object $model): Collection
        {
            return ValidateHelper::environments($model->translationColumns(), $this->databaseConfig->environment);
        }

        /**
         * @param array $attributes
         * @param Collection $columnEnvironment
         * @return array
         */
        private function create(array $attributes, Collection $columnEnvironment): array
        {
            foreach ($attributes as $column => $value) {
                $environment = $columnEnvironment->get($column);

                list($group, $key) = call_user_func($this->databaseKeySeparator, $environment, $value);

                $translation = new TranslationResource();
                $translation->environment = $environment;
                $translation->group = $group;
                $translation->key = $key;

                $translation->setTags($this->databaseConfig->tag);

                $translations[] = $translation;
            }

            return $translations ?? [];
        }
    }
