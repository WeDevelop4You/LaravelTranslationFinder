<?php

    namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;

    /**
     * Class Environment.
     *
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Environment
     *
     * @property-read string $name
     * @property-read Finder $finder
     * @property-read Storage $storage
     */
    class Environment
    {
        /**
         * Environment constructor.
         *
         * @throws EnvironmentNotFoundException
         * @throws UnsupportedFileExtensionException
         */
        public function __construct(string $environment)
        {
            if (!in_array($environment, Config::getEnvironments()->toArray())) {
                throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
            }

            $config = (object) config("translation.environment.options.{$environment}");

            $this->name = $environment;
            $this->finder = new Finder((object) $config->finder);
            $this->storage = new Storage((object) $config->storage);
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
