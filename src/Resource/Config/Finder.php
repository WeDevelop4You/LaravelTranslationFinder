<?php

    namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;

    /**
     * Class Finder.
     *
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Environment
     *
     * @property-read array $tags
     * @property-read array $paths
     * @property-read array $extensions
     * @property-read array $excludePaths
     * @property-read array $functions
     * @property-read array $ignoreGroups
     */
    class Finder
    {
        /**
         * Finder constructor.
         *
         * @param object $finderConfig
         */
        public function __construct(object $finderConfig)
        {
            $this->tags = Arr::wrap($finderConfig->tags);
            $this->paths = Arr::wrap($finderConfig->paths);
            $this->functions = Arr::wrap($finderConfig->functions);
            $this->excludePaths = Arr::wrap($finderConfig->exclude_paths);
            $this->ignoreGroups = Arr::wrap($finderConfig->ignore_groups);
            $this->createValidExtension(Arr::wrap($finderConfig->extensions));
        }

        /**
         * @param array $extensions
         */
        private function createValidExtension(array $extensions): void
        {
            $this->extensions = [];

            foreach ($extensions as $extension) {
                if (!Str::startsWith($extension, '*.')) {
                    $extension = Str::startsWith($extension, '.')
                        ? "*{$extension}"
                        : "*.{$extension}";
                }

                $this->extensions[] = $extension;
            }
        }

        /**
         * @param $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return $this->$name;
        }
    }
