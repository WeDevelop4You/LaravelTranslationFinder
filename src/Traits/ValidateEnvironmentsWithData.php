<?php

    namespace WeDevelop4You\TranslationFinder\Traits;

	use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Exceptions\EnvironmentNotFoundException;

    trait ValidateEnvironmentsWithData
	{
        /**
         * @param array $data
         * @param string $defaultEnvironment
         * @return Collection
         * @throws EnvironmentNotFoundException
         */
        public function validateEnvironmentsWithData(array $data, string $defaultEnvironment): Collection
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
