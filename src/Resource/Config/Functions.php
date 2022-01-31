<?php

    namespace WeDevelop4You\TranslationFinder\Resource\Config;

    use WeDevelop4You\TranslationFinder\Exceptions\ClassNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;

    /**
     * Class Functions.
     *
     * @package WeDevelop4You\TranslationFinder\Resource\Config\Functions
     *
     * @property-read callable get
     * @property-read callable set
     * @property-read callable default
     * @property-read callable database
     */
    class Functions
    {
        private const FUNCTIONS = [
            'get' => 'file_content',
            'set' => 'file_content',
            'default' => 'key_separator',
            'database' => 'key_separator',
        ];

        /**
         * Functions constructor.
         *
         * @throws ClassNotFoundException|MethodNotCallableException
         */
        public function __construct()
        {
            foreach (config('translation.helpers') as $name => $class) {
                if (!class_exists($class)) {
                    throw new ClassNotFoundException("Class [{$class}] doesn't exist");
                }

                ${$name} = $class;
            }

            foreach (self::FUNCTIONS as $functionName => $classVariable) {
                $this->createValidateFunction($$classVariable, $functionName);
            }
        }

        /**
         * Checks if functions is callable and creates the function property.
         *
         * @throws MethodNotCallableException
         */
        private function createValidateFunction(string $class, string $functionName): void
        {
            $function = "{$class}::{$functionName}";

            if (!is_callable($function)) {
                throw new MethodNotCallableException("Method [{$function}] not found or callable");
            }

            $this->$functionName = $function;
        }

        /**
         * @param $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return $this->$name;
        }
    }
