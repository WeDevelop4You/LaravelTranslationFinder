<?php


	namespace WeDevelop4You\TranslationFinder\Classes;


    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use Symfony\Component\Console\Helper\ProgressBar;
    use Symfony\Component\Console\Output\ConsoleOutput;
    use Symfony\Component\Finder\Finder as FileFinder;
    use WeDevelop4You\TranslationFinder\Resource\Translation\Found;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;
    use WeDevelop4You\TranslationFinder\Models\Translation;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;
    use WeDevelop4You\TranslationFinder\Models\TranslationSource;
    use WeDevelop4You\TranslationFinder\Resource\Config\Environment;
    use WeDevelop4You\TranslationFinder\Resource\Config\Finder;

    class Search
	{
	    private const DEFAULT_EXCLUDE_PATHS = [
            'vendor',
            'storage/framework/views'
        ];

        /**
         * @var Collection|Found[]
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
         */
        public function __construct()
        {
            $this->config = Config::build();
            $this->saveCounter = new Collection();
            $this->translations = new Collection();

            $this->config->environments->each(function (Environment $environment) {
                $name = $environment->name;

                $this->saveCounter->put($name, 0);
                $this->find($environment->finder, $name);
            });

            $this->save();
        }

        /**
         * @param Finder $finderConfig
         * @param string $environment
         */
        private function find(Finder $finderConfig, string $environment): void
        {
            $groupPattern =                                             // See https://regex101.com/r/WEJqdL/6
                "[\W]".                                                 // Must not have an alphanum or _ or > before real method
                '('.implode('|', $finderConfig->functions).')'. // Must start with one of the functions
                "\(".                                                   // Match opening parenthesis
                "[\'\"]".                                               // Match " or '
                '('.                                                    // Start a new group to match:
                '[a-zA-Z0-9_-]+'.                                       // Must start with group
                "([.](?! )[^\1)]+)+".                                   // Be followed by one or more items/keys
                ')'.                                                    // Close group
                "[\'\"]".                                               // Closing quote
                "[\),]";                                                // Close parentheses or new parameter

            $stringPattern =
                "[^\w]".                                                // Must not have an alphanum before real method
                '('.implode('|', $finderConfig->functions).')'. // Must start with one of the functions
                "\(\s*".                                                // Match opening parenthesis
                "(?P<quote>['\"])".                                     // Match " or ' and store in {quote}
                "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)".          // Match any string that can be {quote} escaped
                "\k{quote}".                                            // Match " or ' previously matched
                "\s*[\),]";                                             // Close parentheses or new parameter

            $finder = new FileFinder();
            $finder->in($finderConfig->path)
                ->exclude(self::DEFAULT_EXCLUDE_PATHS)
                ->exclude($finderConfig->excludePaths)
                ->name($finderConfig->extension)
                ->files();

            if (App::runningInConsole()) {
                $output = new ConsoleOutput();
                $section = $output->section();

                $bar = new ProgressBar($section);
                $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
                $bar->setMessage("Searching files in environment: {$environment}");
                $bar->start($finder->count());
            }

            foreach ($finder as $file) {
                if (isset($bar)) {
                    $bar->advance();
                }

                if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches[2] as $index => $translationKey) {
                        list($group, $key) = call_user_func($this->config->keySeparator, $environment, $translationKey);

                        $translation = $this->create($environment, $group, $key);
                        $translation->findLineNumberInFile($file, $matches[0][$index]);
                    }
                }

                if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches['string'] as $index =>  $translationKey) {
                        if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\/]+)+$)/siU", $translationKey, $groupMatches)) {
                            // group{.group}.key format, already in $groupKeys but also matched here
                            // do nothing, it has to be treated as a group
                            continue;
                        }

                        if (!(Str::contains($translationKey, '::') && Str::contains($translationKey, '.')) || Str::contains($translationKey, ' ')) {
                            $translation = $this->create($environment, '_json', $translationKey);
                            $translation->findLineNumberInFile($file, $matches[0][$index]);
                        }
                    }
                }
            }

            if (isset($bar)) {
                $bar->finish();
            }
        }

        private function save(): void
        {
            TranslationSource::truncate();

            $bar = null;

            if (App::runningInConsole()) {
                $output = new ConsoleOutput();
                $section = $output->section();

                $bar = new ProgressBar($section);
                $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
                $bar->setMessage("Translations in database");
                $bar->start($this->translations->count());
            }

            $this->translations->each(function (Found $translation) use ($bar){
                $key = $translation->key;
                $group = $translation->group;
                $environment = $translation->environment;

                $translationKey = TranslationKey::where('environment', $environment)
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
                        $value->locale = config('app.fallback_locale');
                        $value->translation = $key;
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

                if (isset($bar)) {
                    $bar->advance();
                }
            });

            if (isset($bar)) {
                $bar->finish();
            }
        }

        /**
         * @param string $environment
         * @param string $group
         * @param string $key
         * @return Found
         */
        private function create(string $environment, string $group, string $key): Found
        {
            $translation = $this->translations->where('environment', $environment)->where('key', $key)->where('group', $group)->first();

            if (is_null($translation)) {
                $translation = new Found();
                $translation->key = $key;
                $translation->group = $group;
                $translation->environment = $environment;

                $this->translations->push($translation);
            }

            return $translation;
        }
    }
