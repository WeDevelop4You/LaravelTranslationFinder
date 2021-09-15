<?php

	namespace WeDevelop4You\TranslationFinder\Classes\Bootstrap;

	use Exception;
    use Illuminate\Contracts\Filesystem\FileNotFoundException;
    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Facades\App;

    class TranslationPackagesPath extends BootstrapCache
	{
        public function __construct()
        {
            $this->file = new Filesystem();
            $this->storagePath = App::bootstrapPath('cache/translation-packages.php');
        }

        /**
         * @param int $id
         * @param string $path
         * @throws FileNotFoundException|Exception
         */
        public function add(int $id, string $path)
        {
            $content = $this->get(true);
            $this->data = $content->mergeRecursive([$path => $id]);
            $this->set();
        }

        /**
         * @param int $id
         * @return false|string
         * @throws FileNotFoundException
         */
        public function has(int $id)
        {
            $found = false;

            $this->get(true)->each(function(array $ids, string $path) use ($id, &$found) {
                if (in_array($id, $ids)) {
                    $found = $path;
                }
            });

            return $found;
        }

        public function reset()
        {
            $this->create();
        }
	}
