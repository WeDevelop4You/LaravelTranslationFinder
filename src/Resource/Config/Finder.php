<?php


	namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Str;

    /**
     * Class Finder
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Environment
     *
     * @property-read string $tag
     * @property-read string $path
     * @property-read string $extension
     * @property-read array $excludePaths
     * @property-read array $functions
     * @property-read array $ignoreGroups
     */
	class Finder
	{
        /**
         * Finder constructor.
         * @param object $finderConfig
         */
        public function __construct(object $finderConfig)
        {
            $this->tag = $finderConfig->tag;
            $this->path = $finderConfig->path;
            $this->functions = $finderConfig->functions;
            $this->excludePaths = $finderConfig->exclude_paths;
            $this->ignoreGroups = $finderConfig->ignore_groups;
            $this->createValidExtension($finderConfig->extension);
        }

        /**
         * @param string $extension
         */
        private function createValidExtension(string $extension): void
        {
            if (!Str::startsWith($extension, '*.')) {
                $extension = Str::startsWith($extension, '.')
                    ? "*{$extension}"
                    : "*.{$extension}";
            }

            $this->extension = $extension;
        }

        /**
         * @param $name
         * @return mixed
         */
        public function __get($name)
        {
            return $this->$name;
        }
    }
