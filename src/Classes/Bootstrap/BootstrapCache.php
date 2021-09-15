<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Bootstrap;

	use Exception;
    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Collection;

    class BootstrapCache
	{
        /**
         * @var Collection|array
         */
        protected $data;

        /**
         * @var Filesystem
         */
        protected Filesystem $file;

        /**
         * @var string
         */
        protected string $storagePath;

        /**
         * @param bool $inCollection
         * @return array|Collection
         * @throws FileNotFoundException|Exception
         */
        public function get(bool $inCollection = false)
        {
            if (isset($this->data)) {
                return $this->data;
            }

            if (!file_exists($this->storagePath)) {
                $this->create();
            }

            $content = $this->file->getRequire($this->storagePath);

            return $this->data = $inCollection
                ? new Collection($content)
                : $content;
        }

        /**
         * @return BootstrapCache
         * @throws Exception
         */
        public function set(): BootstrapCache
        {
            $directory = dirname($this->storagePath);

            if (!is_writable($directory)) {
                throw new Exception("The {$directory} directory must be present and writable.");
            }

            $data = $this->data instanceof Collection
                ? $this->data->toArray()
                : $this->data;

            $content = var_export($data, true);
            $this->file->put($this->storagePath, "<?php return {$content};\n", true);

            return $this;
        }

        /**
         * @return BootstrapCache
         */
        protected function create(): BootstrapCache
        {
            $this->file->put($this->storagePath, "<?php return array();\n", true);

            return $this;
        }
    }
