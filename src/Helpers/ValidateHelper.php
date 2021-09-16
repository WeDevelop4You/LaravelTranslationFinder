<?php

    namespace WeDevelop4You\TranslationFinder\Helpers;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\ParameterRequiredException;

    class ValidateHelper
    {
        /**
         * Gets the environment and checks if the environment is valid
         *
         * @param string|null $environment
         * @return string
         * @throws EnvironmentNotFoundException|ParameterRequiredException
         */
        public static function environment(?string $environment = null): string
        {
            if (is_null($environment)) {
                if (Config::isEnvironmentsSeparated()) {
                    throw new ParameterRequiredException("The [environment] parameter is required when using separated environments.");
                } else {
                    $environment = Config::DEFAULT_ENVIRONMENT;
                }
            } elseif (!in_array($environment, Config::getEnvironments()->toArray())) {
                throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
            }

            return $environment;
        }

        /**
         * @param array $data
         * @param string $defaultEnvironment
         * @return Collection
         * @throws EnvironmentNotFoundException
         */
        public static function environments(array $data, string $defaultEnvironment): Collection
        {
            $dataWithEnvironments = new Collection();
            $environments = Config::getEnvironments()->toArray();
            $isEnvironmentsSeparated = Config::isEnvironmentsSeparated();

            foreach ($data as $index => $value) {
                if (is_string($index)) {
                    $column = $index;
                    $environment = $value;

                    if (!$isEnvironmentsSeparated && $environment !== $defaultEnvironment) {
                        throw (new EnvironmentNotFoundException())->setMessageNotDefaultEnvironment($environment, $defaultEnvironment);
                    }
                } else {
                    $column = $value;
                    $environment = $defaultEnvironment;
                }

                if (!in_array($environment, $environments)) {
                    throw (new EnvironmentNotFoundException())->setMessageEnvironmentDoesNotExist($environment);
                }

                $dataWithEnvironments->put($column, $environment);
            }

            return $dataWithEnvironments;
        }
    }
