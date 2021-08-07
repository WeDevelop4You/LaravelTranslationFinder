<?php

    namespace WeDevelop4You\TranslationFinder\Classes;

    use Illuminate\Support\Collection;
    use WeDevelop4You\TranslationFinder\Exceptions\ClassNotFoundException;
    use WeDevelop4You\TranslationFinder\Exceptions\MethodNotCallableException;
    use WeDevelop4You\TranslationFinder\Resource\Config\Environment;

    /**
     * Class ConfigBuilder
     * @package WeDevelop4You\Translation\Classes
     *
     * @property-read callable buildFile
     * @property-read callable getFileData
     * @property-read callable keySeparator
     * @property-read bool useTranslationSource
     * @property-read Collection|Environment[] $environments
     */
    class Config
	{
	    /**
         * ConfigBuilder constructor.
         * @throws MethodNotCallableException|ClassNotFoundException
         */
        public function __construct()
        {
            $this->createFunctions();
            $this->environments = new Collection();
            $this->useTranslationSource = config('translation.use_translation_source');

            if (config('translation.environment.separate_environment')) {
                $environments[] = new Environment('frontend');
                $environments[] = new Environment('backend');
            } else {
                $environments[] = new Environment('default');
            }

            $this->environments->push(...$environments);
        }

        /**
         * @return Config
         */
        public static function build(): Config
        {
            return new static();
        }

        /**
         * @throws MethodNotCallableException|ClassNotFoundException
         */
        private function createFunctions()
        {
            $keyClass = config('translation.key_class');
            $fileClass = config('translation.file_class');

            if (!class_exists($keyClass) && !class_exists($fileClass)) {
                throw new ClassNotFoundException();
            }

            $this->validateFunction($keyClass, 'keySeparator');
            $this->validateFunction($fileClass, 'getFileData');
            $this->validateFunction($fileClass, 'buildFile');
        }

        /**
         * @throws MethodNotCallableException
         */
        private function validateFunction(string $class, string $functionName): void
        {
            $function = "{$class}::{$functionName}";

            if (!is_callable($function)) {
                throw new MethodNotCallableException("Method [{$function}] not found or callable");
            }

            $this->$functionName = $function;
        }

        /**
         * @param $name
         * @return mixed
         */
        public function __get($name)
        {
            return $this->$name;
        }
    }
