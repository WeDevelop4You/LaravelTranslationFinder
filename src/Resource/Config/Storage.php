<?php


	namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Classes\File;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;
    use WeDevelop4You\TranslationFinder\Providers\TranslationFinderServiceProvider;

    /**
     * Class Storage
     * @package WeDevelop4You\Translation\Classes\Environment
     *
     * @property-read string $path
     * @property-read string $extension
     */
	class Storage
	{
        /**
         * Storage constructor.
         * @param object $storageConfig
         * @throws UnsupportedFileExtensionException
         */
        public function __construct(object $storageConfig)
        {
            $this->path = $storageConfig->path;
            $this->createValidExtension(strtolower($storageConfig->extension));
        }

        /**
         * @param string $extension
         * @return void
         * @throws UnsupportedFileExtensionException
         */
        private function createValidExtension(string $extension): void
        {
            if (Str::contains($extension, File::SUPPORTED_FILE_EXTENSIONS)) {
                $this->extension = Str::startsWith($extension, '.') ? $extension : ".{$extension}";
            } else {
                throw new UnsupportedFileExtensionException("File extension [{$extension}] is not supported");
            }
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
