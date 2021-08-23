<?php


    namespace WeDevelop4You\TranslationFinder\Classes\Database;

    use Exception;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;
    use ReflectionClass;
    use Illuminate\Filesystem\Filesystem;
    use Symfony\Component\Finder\SplFileInfo;

	class ModelFinder
	{
        /**
         * @var string
         */
        private string $path;

        /**
         * @var Filesystem
         */
        private Filesystem $file;

        /**
         * @var array
         */
        private array $data;

        /**
         * @var string
         */
        private string $storagePath;

        /**
         * @throws Exception
         */
        public function __construct()
        {
            $this->file = new Filesystem();
            $this->path = config('translation.database.model_path');
            $this->storagePath = App::bootstrapPath('cache/translation-models.php');
        }

        /**
         * @return ModelFinder
         * @throws Exception
         */
        public function build(): ModelFinder
        {
            $this->getClassNames();
            $this->store();

            return $this;
        }

        /**
         * @return array
         * @throws Exception
         */
        public function get(): array
        {
            if (isset($this->data)) {
                return $this->data;
            }

            if (!file_exists($this->storagePath)) {
                $this->build();
            }

            return $this->data = $this->file->getRequire($this->storagePath);
        }


        /**
         * @throws Exception
         */
        private function store()
        {
            $directory = dirname($this->storagePath);

            if (!is_writable($directory)) {
                throw new Exception("The {$directory} directory must be present and writable.");
            }

            $content = var_export($this->data, true);
            $this->file->put($this->storagePath, "<?php return {$content};\n", true);
        }

        private function getClassNames()
        {
            $this->data = collect($this->file->allFiles($this->path))
                ->map(function (SplFileInfo $file) {
                    return App::getNamespace().
                        Str::of($file->getPathname())
                            ->after(App::basePath().'/')
                            ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(function (string $class) {
                    return is_subclass_of($class, Translation::class) &&
                        ! (new ReflectionClass($class))->isAbstract();
                })->toArray();
        }
	}
