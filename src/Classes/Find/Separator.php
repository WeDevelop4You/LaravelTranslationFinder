<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Find;

	class Separator
	{
        /**
         * @param string $environment
         * @param string $translationKey
         * @return array
         */
        public static function default(string $environment, string $translationKey): array
        {
            if ($environment === 'frontend') {
                list(, $group, $name) = explode('.', $translationKey, 3);

                $key = "{$group}.{$name}";

                return [$group, $key];
            }

            return explode('.', $translationKey, 2);
        }

        /**
         * @param string $environment
         * @param string $translationKey
         * @return array
         */
        public static function database(string $environment, string $translationKey): array
        {
            if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\/]+)+$)/siU", $translationKey, $groupMatches)) {
                return explode('.', $translationKey, 2);
            }

            return ['_json', $translationKey];
        }
	}
