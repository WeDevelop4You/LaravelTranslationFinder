<?php

    namespace WeDevelop4You\TranslationFinder\Helpers;

    use Illuminate\Support\Facades\Schema;
    use WeDevelop4You\TranslationFinder\Classes\Config;
    use WeDevelop4You\TranslationFinder\Models\Translation;
    use WeDevelop4You\TranslationFinder\Models\TranslationKey;
    use WeDevelop4You\TranslationFinder\Models\TranslationSource;

    class TruncateTableHelper
    {
        public static function sources()
        {
            if (Config::isTranslationsSourceUsed()) {
                TranslationSource::truncate();
            }
        }

        public static function all()
        {
            self::sources();

            Translation::truncate();

            Schema::disableForeignKeyConstraints();
            TranslationKey::truncate();
            Schema::enableForeignKeyConstraints();
        }
    }
