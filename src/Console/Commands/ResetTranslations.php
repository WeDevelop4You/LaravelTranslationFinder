<?php

namespace WeDevelop4You\TranslationFinder\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use WeDevelop4You\TranslationFinder\Classes\Manager;
use WeDevelop4You\TranslationFinder\Models\Translation;
use WeDevelop4You\TranslationFinder\Models\TranslationKey;
use WeDevelop4You\TranslationFinder\Models\TranslationSource;

class ResetTranslations extends Command
{
    /**
     * The name and signature of the console command.-
     *
     * @var string
     */
    protected $signature = 'translation:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipes all translations tables in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        TranslationSource::truncate();
        Translation::truncate();
        Schema::disableForeignKeyConstraints();
        TranslationKey::truncate();
        Schema::enableForeignKeyConstraints();

        $this->info('Database translation tables have been reset');
    }
}
