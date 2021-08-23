<?php

	namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Traits\ValidateEnvironmentsWithData;

    /**
     * Class Packages
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Database
     *
     * @property-read bool getTranslations
     * @property-read Collection $paths
     */
	class Packages
	{
	    use ValidateEnvironmentsWithData;

        /**
         * Packages constructor.
         *
         * @param object $config
         * @throws EnvironmentNotFoundException
         */
        public function __construct(object $config)
        {
            $this->getTranslations = $config->get_translations;
            $this->paths = $this->validateEnvironmentsWithData($config->paths, $config->default_environment);
        }
    }
