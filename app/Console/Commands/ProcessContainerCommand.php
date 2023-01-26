<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Service\ContainerService;

class ProcessContainerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:container {file} {--t}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обрабытывает указанный контейнер';

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
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        echo 'Обрабатывается '.$file.PHP_EOL;
        $path = $this->option('t')? './tests/Resources/' : './public/income/';
        $container = ContainerService::ProcessFile($path.$file);
        if ($container->success) {
            $doc = $container->DocumentTitle;

            echo PHP_EOL . 'Добавлен новый документ' . PHP_EOL . $doc->title . '(' . $doc->document_reference . ')' . PHP_EOL;
            return 0;
        }
        else{
            echo 'Ошибка при обработке контейнера:' . PHP_EOL
                . $container->error . PHP_EOL;
            return 1;
        }


    }
}
