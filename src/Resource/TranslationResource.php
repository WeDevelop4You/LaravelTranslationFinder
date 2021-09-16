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
         * @var array
         */
        private array $tags;

        /**
         * @var string
         */
        public string $value;

        /**
         * @var string
         */
        public string $path;


        /**
         * @var Collection
         */
        public Collection $sources;

        public function __construct()
        {
            $this->sources = new Collection();
        }

        /**
         * @return array|null
         */
        public function getTags(): ?array
        {
            return $this->tags ?? null;
        }

        /**
         * @param array|string|null $tags
         */
        public function setTags($tags): void
        {
            $tags = is_array($tags) ? $tags : [$tags];

            foreach ($tags as $tag) {
                if (! empty($tag)) {
                    $this->tags[] = $tag;
                }
            }
        }

        /**
         * @param SplFileInfo $file
         * @param $search
         * @return TranslationResource
         */
        public function findLineNumberInFile(SplFileInfo $file, $search): TranslationResource
        {
            $lines = file($file->getRealPath());

            foreach ($lines as $key => $line) {
                if (Str::contains($line, $search)) {
                    $lineNumber = $key + 1;
                    $source = "{$file->getRelativePath()}/{$file->getFilename()}:{$lineNumber}";

                    if (! $this->sources->contains($source)) {
                        $this->sources->push($source);
                    }
                }
            }

            return $this;
        }
    }
