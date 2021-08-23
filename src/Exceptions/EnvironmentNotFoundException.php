<?php


    namespace WeDevelop4You\TranslationFinder\Exceptions;


	use Exception;

    class EnvironmentNotFoundException extends Exception
	{
        /**
         * @param string $environment
         * @param string $defaultEnvironment
         * @return EnvironmentNotFoundException
         */
        final public function setMessageNotDefaultEnvironment(string $environment, string $defaultEnvironment): EnvironmentNotFoundException
        {
            $this->message = "Environment [{$environment}] is not allowed when the separated environments is not used, only environment [$defaultEnvironment] is allowed";

            return $this;
        }

        /**
         * @param string $environment
         * @return EnvironmentNotFoundException
         */
        final public function setMessageEnvironmentDoesNotExist(string $environment): EnvironmentNotFoundException
        {
            $this->message = "Environment [{$environment}] doesn't exist in the translation config file";

            return $this;
        }
	}
