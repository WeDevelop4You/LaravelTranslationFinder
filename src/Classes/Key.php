<?php

	namespace WeDevelop4You\TranslationFinder\Classes;

	class Key
	{
        /**
         * @param string $environment
         * @param string $translationKey
         * @return array
         */
        public static function keySeparator(string $environment, string $translationKey): array
        {
            if ($environment === 'frontend') {
                list(, $group, $name) = explode('.', $translationKey, 3);

                $key = "{$group}.{$name}";

                return [$group, $key];
            }

            return explode('.', $translationKey, 2);
        }
	}
