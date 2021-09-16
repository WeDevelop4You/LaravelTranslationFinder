<?php

    namespace WeDevelop4You\TranslationFinder\Helpers;

    use Symfony\Component\Console\Helper\ProgressBar;
    use Symfony\Component\Console\Output\ConsoleOutput;

    class ProgressBarHelper
    {
        private ProgressBar $progressBar;

        public function __construct(string $message, int $total)
        {
            $output = new ConsoleOutput();
            $section = $output->section();

            $progressBar = new ProgressBar($section);
            $progressBar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%\n");
            $progressBar->setMessage($message);
            $progressBar->start($total);

            $this->progressBar = $progressBar;
        }

        public function add()
        {
            $this->progressBar->advance();
        }

        public function finish()
        {
            $this->progressBar->finish();
        }
    }
