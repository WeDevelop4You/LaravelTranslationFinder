<?php


    namespace WeDevelop4You\TranslationFinder\Resource;


	use Illuminate\Support\Collection;
    use Illuminate\Support\Str;
    use Symfony\Component\Finder\SplFileInfo;

    class TranslationResource
	{
        /**
         * @var string
         */
        public string $environment;

        /**
         * @var string
         */
        public string $group;

        /**
         * @var string
         */
        public string $key;

        /**
         * @var Collection
         */
        public Collection $sources;

        public function __construct()
        {
            $this->sources = new Collection();
        }

        /**
         * @param SplFileInfo $file
         * @param $search
         * @return void
         */
        public function findLineNumberInFile(SplFileInfo $file, $search): void
        {
            $lines = file($file->getRealPath());

            foreach ($lines as $key => $line) {
                if (Str::contains($line, $search)) {
                    $lineNumber = $key + 1;
                    $source = "{$file->getRelativePath()}/{$file->getFilename()}:{$lineNumber}";

                    if (!$this->sources->contains($source)) {
                        $this->sources->push($source);
                    }
                }
            }
        }
	}
