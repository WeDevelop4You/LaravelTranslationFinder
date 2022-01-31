<?php

namespace WeDevelop4You\TranslationFinder\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use WeDevelop4You\TranslationFinder\Classes\Manager;

class FindTranslations extends Command
{
    /**
     * The name and signature of the console command.-.
     *
     * @var string
     */
    protected $signature = 'translation:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find the translations in project';

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
        try {
            $totalFound = Manager::search();

            $this->info('Successfully scanned files');
            $totalFound->each(function (int $value, string $key) {
                $environment = ucfirst($key);

                $this->info("In environment [{$environment}] total new found: {$value}");
            });
        } catch (Exception $e) {
            $this->error(' failed ');
            $this->line($e->getMessage());
        }
    }
}
