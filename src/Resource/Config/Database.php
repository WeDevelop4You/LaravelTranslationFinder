<?php

    namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;

    /**
     * Class Finder
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Database
     *
     * @property-read string $tag
     * @property-read bool searchModels
     * @property-read string $environment
     */
    class Database
    {
        /**
         * Database constructor.
         *
         * @param object $config
         * @throws EnvironmentNotFoundException
         */
        public function __construct(object $config)
        {
            $this->tag = $config->tag;
            $this->searchModels = $config->search_models;
            $this->setEnvironment($config->default_environment);
        }

        /**
         * @param string $environment
         * @throws EnvironmentNotFoundException
         */
        private function setEnvironment(string $environment): void
        {
            $defaultEnvironment = Config::DEFAULT_ENVIRONMENT;

            if (! Config::isEnvironmentsSeparated() && $environment !== $defaultEnvironment) {
                throw (new EnvironmentNotFoundException())->setMessageNotDefaultEnvironment($environment, $defaultEnvironment);
            } elseif (! in_array($environment, Config::getEnvironments()->toArray())) {
                throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
            }

            $this->environment = $environment;
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
