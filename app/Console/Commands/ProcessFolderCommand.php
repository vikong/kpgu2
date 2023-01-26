<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Service\ContainerService;

class ProcessFolderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:folder {folder?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обрабытывает все контейнеры в указанной папке';

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
        if($this->hasArgument('folder')){
            $folder = $this->argument('folder');
        }
        if (!isset($folder)) {
            $folder = './public/income/';
        }

        ContainerService::ProcessFolder($folder);

        echo PHP_EOL.'Добавлено'.PHP_EOL.PHP_EOL;
        return 0;
    }
}
