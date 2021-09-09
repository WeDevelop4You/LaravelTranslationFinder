<?php


	namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Str;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;

    /**
     * Class Storage
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Environment
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
            $fileClass = config('translation.helpers.file');

            if (Str::contains($extension, constant("{$fileClass}::SUPPORTED_FILE_EXTENSIONS"))) {
                $this->extension = ltrim($extension, '.');
            } else {
                throw new UnsupportedFileExtensionException("Packages extension [{$extension}] is not supported");
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
