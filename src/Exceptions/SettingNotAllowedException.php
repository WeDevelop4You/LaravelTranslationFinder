<?php

	namespace WeDevelop4You\TranslationFinder\Exceptions;

	use Exception;

    class SettingNotAllowedException extends Exception
	{
        /**
         * @return $this
         */
        public function tableDoesNotExist(string $table): SettingNotAllowedException
        {
            $this->message = "Table [{$table}] doesn't exist run 'php artisan migrate'";

            return $this;
        }
	}
