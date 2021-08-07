<?php

namespace WeDevelop4You\TranslationFinder\Console\Commands;

use Illuminate\Console\Command;
use WeDevelop4You\TranslationFinder\Classes\Manager;
use WeDevelop4You\TranslationFinder\Exceptions\FailedToBuildTranslationFileException;

class PublishTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the translations';

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
    public function handle()
    {
        try {
            Manager::publishAll();

            $this->info('success');
        } catch (FailedToBuildTranslationFileException $e) {
            $this->error('failed');
        }
    }
}
