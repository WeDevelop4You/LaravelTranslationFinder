<?php

	namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Helpers\ValidateHelper;
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
	    /**
         * Packages constructor.
         *
         * @param object $config
         * @throws EnvironmentNotFoundException
         */
        public function __construct(object $config)
        {
            $this->getTranslations = $config->get_translations;
            $this->paths = ValidateHelper::environments($config->paths, $config->default_environment);
        }
    }
