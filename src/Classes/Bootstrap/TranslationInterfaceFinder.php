<?php

    namespace WeDevelop4You\TranslationFinder\Classes\Bootstrap;

    use Exception;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use ReflectionClass;
    use Symfony\Component\Finder\SplFileInfo;
    use WeDevelop4You\TranslationFinder\Interfaces\Translation;

    class TranslationInterfaceFinder extends BootstrapCache
    {
        /**
         * @var string
         */
        private string $path;

        public function __construct()
        {
            $this->file = new Filesystem();
            $this->path = config('translation.database.model_path');
            $this->storagePath = App::bootstrapPath('cache/translation-models.php');
        }

        /**
         * @return TranslationInterfaceFinder
         * @throws Exception
         */
        public static function run(): TranslationInterfaceFinder
        {
            $modelFinder = new static();
            $modelFinder->getClassNames()->set();

            return $modelFinder;
        }

        /**
         * @return TranslationInterfaceFinder
         */
        private function getClassNames(): TranslationInterfaceFinder
        {
            $this->data = collect($this->file->allFiles($this->path))
                ->map(function (SplFileInfo $file) {
                    return App::getNamespace().
                        Str::of($file->getPathname())
                            ->after(app_path().'/')
                            ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(function (string $class) {
                    return is_subclass_of($class, Translation::class) &&
                        !(new ReflectionClass($class))->isAbstract();
                });

            return $this;
        }
    }
