<?php

	namespace WeDevelop4You\TranslationFinder\Classes;

	use WeDevelop4You\TranslationFinder\Exceptions\UnsupportedFileExtensionException;

    class File
	{
	    public const SUPPORTED_FILE_EXTENSIONS = [
            'php',
            'json'
        ];

        /**
         * @param string $fullPath
         * @return array
         * @throws UnsupportedFileExtensionException
         */
        public static function getFileData(string $fullPath): array
        {
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            switch ($extension) {
                case 'php':
                    return file_exists($fullPath) ? include $fullPath : [];
                case 'json':
                    return json_decode(file_get_contents($fullPath), true);
                default:
                    throw new UnsupportedFileExtensionException("File extension [{$extension}] is not supported");
            }
        }

        /**
         * @param string $fullPath
         * @param array $translations
         * @return string
         * @throws UnsupportedFileExtensionException
         */
        public static function buildFile(string $fullPath, array $translations): string
        {
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            switch ($extension) {
                case 'php':
                    $content = var_export($translations, true);
                    return "<?php\n\nreturn {$content};\n";
                case 'json':
                    return json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                default:
                    throw new UnsupportedFileExtensionException("File extension [{$extension}] is not supported");
            }
        }
	}
