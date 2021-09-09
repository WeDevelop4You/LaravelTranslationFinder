<?php


	namespace WeDevelop4You\TranslationFinder\Classes\Find;


    use Exception;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Models\Translation;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey as TranslationKeyModel;
    use WeDevelop4You\TranslationFinder\Models\TranslationSource;
    use WeDevelop4You\TranslationFinder\Resource\Config\Environment;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class Finder
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
         * Search constructor.
         * @throws Exception
         */
        public function __construct()
        {
            $this->config = Config::build();

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
                $name = $environment->name;

                $project = new ProjectSearcher($environment->finder, $this->config->functions->default, $name);
                $this->translations = $project->find();

                $this->saveCounter->put($name, 0);
            });
        }

        /**
         * @throws Exception
         */
        private function searchInDatabase(): void
        {
            $config = $this->config;

            $database = new DatabaseSearcher($config->functions->database, $config->database->environment);
            $database->find()->each(function(TranslationResource $TranslationResource) {
                $this->removeDuplicates($TranslationResource);
            });
        }

        private function searchInPackages(): void
        {

        }

        /**
         * @param TranslationResource $TranslationResource
         */
        private function removeDuplicates(TranslationResource $TranslationResource): void
        {
            $translation = $this->translations
                ->where('environment', $TranslationResource->environment)
                ->where('group', $TranslationResource->group)
                ->where('key', $TranslationResource->key)
                ->first();

            if (is_null($translation)) {
                $this->translations->push($translation);
            }
        }

        private function save(): void
        {
            TranslationSource::truncate();

            $translations = $this->translations;

            if (App::runningInConsole()) {
                $progressBar = new ProgressBarHelper("Save translations in database", $translations->count());
            } else {
                $progressBar = null;
            }

            $translations->each(function (TranslationResource $translation) use ($progressBar){
                $key = $translation->key;
                $group = $translation->group;
                $environment = $translation->environment;

                $translationKey = TranslationKeyModel::where('environment', $environment)
                    ->where('group', $group)
                    ->where('key', $key)
                    ->firstOrNew();

                if (!$translationKey->exists) {
                    $translationKey->environment = $environment;
                    $translationKey->group = $group;
                    $translationKey->key = $key;
                    $translationKey->save();

                    if (Str::startsWith($group, '_')) {
                        $value = new Translation();
                        $value->translation = $key;
                        $value->locale = $this->config->defaultLocale;
                        $translationKey->translations()->save($value);
                    }

                    $this->saveCounter[$environment] += 1;
                }

                if ($this->config->useTranslationSource) {
                    $translation->sources->each(function (string $source) use ($translationKey) {
                        $translationSource = new TranslationSource();
                        $translationSource->source = $source;
                        $translationKey->sources()->save($translationSource);
                    });
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
