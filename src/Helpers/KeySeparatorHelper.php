<?php

    namespace WeDevelop4You\TranslationFinder\Helpers;

    use WeDevelop4You\TranslationFinder\Classes\Config;

    class KeySeparatorHelper
    {
        /**
         * @param string $environment
         * @param string $translationKey
         *
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
         *
         * @return array
         */
        public static function database(string $environment, string $translationKey): array
        {
            if (preg_match(Config::groupFinder(), $translationKey, $groupMatches)) {
                return explode('.', $translationKey, 2);
            }

            return [Config::DEFAULT_GROUP, $translationKey];
        }
    }
