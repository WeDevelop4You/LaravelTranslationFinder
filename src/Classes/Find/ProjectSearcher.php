<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Find;

	use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use Symfony\Component\Finder\Finder as FileFinder;
    use Symfony\Component\Finder\SplFileInfo;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Helpers\ProgressBarHelper;
    use WeDevelop4You\TranslationFinder\Resource\Config\Finder;
    use WeDevelop4You\TranslationFinder\Resource\TranslationResource;

    class ProjectSearcher
	{
	    private const DEFAULT_EXCLUDE_PATHS = [
            'vendor',
            'storage/framework/views'
        ];

        /**
         * @var string
         */
        private string $environment;

        /**
         * @var Finder
         */
        private Finder $finderConfig;

        /**
         * @var Collection
         */
        private Collection $translations;

        /**
         * @var string
         */
        private string $defaultKeySeparator;

        /**
         * @param Finder $finderConfig
         * @param string $defaultKeySeparator
         * @param string $environment
         */
        public function __construct(Finder $finderConfig, string $defaultKeySeparator, string $environment)
        {
            $this->environment = $environment;
            $this->finderConfig = $finderConfig;
            $this->translations = new Collection();
            $this->defaultKeySeparator = $defaultKeySeparator;
        }

        /**
         * @return Collection
         */
        public function find(): Collection
        {
            $groupPattern =                                             // See https://regex101.com/r/WEJqdL/6
                "[\W]".                                                 // Must not have an alphanum or _ or > before real method
                '('.implode('|', $this->finderConfig->functions).')'. // Must start with one of the functions
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
                '('.implode('|', $this->finderConfig->functions).')'. // Must start with one of the functions
                "\(\s*".                                                // Match opening parenthesis
                "(?P<quote>['\"])".                                     // Match " or ' and Store in {quote}
                "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)".          // Match any string that can be {quote} escaped
                "\k{quote}".                                            // Match " or ' previously matched
                "\s*[\),]";                                             // Close parentheses or new parameter

            $finder = new FileFinder();
            $finder->in($this->finderConfig->path)
                ->exclude(self::DEFAULT_EXCLUDE_PATHS)
                ->exclude($this->finderConfig->excludePaths)
                ->name($this->finderConfig->extension)
                ->files();

            if (App::runningInConsole()) {
                $progressBar = new ProgressBarHelper("Searching files in environment: {$this->environment}", $finder->count());
            }

            foreach ($finder as $file) {
                if (isset($progressBar)) {
                    $progressBar->add();
                }

                if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches[2] as $index => $translationKey) {
                        list($group, $key) = call_user_func($this->defaultKeySeparator, $this->environment, $translationKey);

                        $this->create($group, $key, $file, $matches[0][$index]);
                    }
                }

                if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches['string'] as $index =>  $translationKey) {
                        if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\/]+)+$)/siU", $translationKey, $groupMatches)) {
                            // group{.group}.key format, already in group keys but also matched here
                            // do nothing, it has to be treated as a group
                            continue;
                        }

                        if (!(Str::contains($translationKey, '::') && Str::contains($translationKey, '.')) || Str::contains($translationKey, ' ')) {
                            $this->create(Config::DEFAULT_GROUP, $translationKey, $file, $matches[0][$index]);
                        }
                    }
                }
            }

            if (isset($progressBar)) {
                $progressBar->finish();
            }

            return $this->translations;
        }

        /**
         * @param string $group
         * @param string $key
         * @param SplFileInfo $file
         * @param $search
         */
        private function create(string $group, string $key, SplFileInfo $file, $search): void
        {
            $translation = $this->translations
                ->where('environment', $this->environment)
                ->where('group', $group)
                ->where('key', $key)
                ->first();

            if (is_null($translation)) {
                $translation = new TranslationResource();
                $translation->environment = $this->environment;
                $translation->group = $group;
                $translation->key = $key;

                $translation->setTags($this->finderConfig->tag);

                $this->translations->push($translation);
            }

            $translation->findLineNumberInFile($file, $search);

        }
	}
