<?php

namespace WeDevelop4You\TranslationFinder\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use WeDevelop4You\TranslationFinder\Classes\Database\ModelFinder;
use WeDevelop4You\TranslationFinder\Classes\Manager;

class DiscoverTranslationModels extends Command
{
    /**
     * The name and signature of the console command.-
     *
     * @var string
     */
    protected $signature = 'translation:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find the models where translations is used';

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
            $finder = new ModelFinder();
            $finder->build();

            $this->info('Successfully discovered all models with translation class');
        } catch (Exception $e) {
            $this->error(' failed ');
            $this->line($e->getMessage());
        }
    }
}
