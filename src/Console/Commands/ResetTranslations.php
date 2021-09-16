<?php

namespace WeDevelop4You\TranslationFinder\Console\Commands;

use Illuminate\Console\Command;
use WeDevelop4You\TranslationFinder\Helpers\TruncateTableHelper;

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
        TruncateTableHelper::all();

        $this->info('Database translation tables have been reset');
    }
}
