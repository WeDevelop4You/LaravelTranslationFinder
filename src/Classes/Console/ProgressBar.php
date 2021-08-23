<?php


    namespace WeDevelop4You\TranslationFinder\Classes\Console;

    use Symfony\Component\Console\Helper\ProgressBar as ConsoleProgressBar;
    use Symfony\Component\Console\Output\ConsoleOutput;

    class ProgressBar
	{
	    private ConsoleProgressBar $progressBar;

        public function __construct(string $message, int $total)
        {
            $output = new ConsoleOutput();
            $section = $output->section();

            $progressBar = new ConsoleProgressBar($section);
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
