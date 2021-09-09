<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Find;

	use App\Models\User;
    use Exception;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use WeDevelop4You\TranslationFinder\Classes\Database\ModelFinder;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Helpers\ValidateHelper;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class DatabaseSearcher
	{
        /**
         * @var Collection
         */
        private Collection $modelClasses;

        /**
         * @var string
         */
        private string $defaultEnvironment;

        /**
         * @var string
         */
        private string $databaseKeySeparator;

        /**
         * Search constructor.
         * @throws Exception
         */
        public function __construct(string $databaseKeySeparator, string $defaultEnvironment)
        {
            $finder = new ModelFinder();

            $this->defaultEnvironment = $defaultEnvironment;
            $this->databaseKeySeparator = $databaseKeySeparator;
            $this->modelClasses = new Collection($finder->get());
        }

        /**
         * @return Collection
         */
        public function find(): Collection
        {
            return $this->modelClasses->flatMap(function($modelClass) {
                $columnEnvironment = $this->getTranslationColumns(new $modelClass());

                if (App::runningInConsole()) {
                    $progressBar = new ProgressBarHelper("Searching database rows in model: {$modelClass}", $modelClass::count());
                } else {
                    $progressBar = null;
                }

                if (!is_null($columnEnvironment)) {
                    $columns = $columnEnvironment->keys()->toArray();

                    $modelTranslations = User::all($columns)->flatMap(function($data) use ($columnEnvironment, $progressBar) {
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
         * @param $model
         * @return Collection
         * @throws EnvironmentNotFoundException
         */
        private function getTranslationColumns($model): Collection
        {
            $defaultEnvironment = $this->defaultEnvironment;

            return ValidateHelper::environments($model->translationColumns(), $defaultEnvironment);
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

                $translations[] = $translation;

            }

            return $translations ?? [];
        }
	}
