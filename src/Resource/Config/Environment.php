<?php


    namespace WeDevelop4You\TranslationFinder\Resource\Config;


    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;

    /**
     * Class Environment
     * @package WeDevelop4You\Translation\Classes\Environment
     *
     * @property-read string $name
     * @property-read Finder $finder
     * @property-read Storage $storage
     */
    class Environment
    {
        /**
         * Environment constructor.
         * @throws EnvironmentNotFoundException
         * @throws UnsupportedFileExtensionException
         */
        public function __construct(string $environment)
        {
            if (!in_array($environment, array_keys(config('translation.environment.options', [])))) {
                throw new EnvironmentNotFoundException("Environment [{$environment}] doesn't exist in the translation config file");
            }

            $config = (object) config("translation.environment.options.{$environment}");

            $this->name = $environment;
            $this->finder = new Finder((object) $config->finder);
            $this->storage = new Storage((object) $config->storage);
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
