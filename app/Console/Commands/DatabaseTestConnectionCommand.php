<?php

namespace App\Console\Commands;

use App\Service\XmlReader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class DatabaseTestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Конфигурация соединиения с базой данных';

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
        
        $defaultConn = Config::get('database.default');
        $conn = Config::get('database.connections.'.$defaultConn);
        echo "Подключаюсь к: {$conn['host']}:{$conn['port']}; database={$conn['database']}; user={$conn['username']};".PHP_EOL;

        //var_dump($defaultConn["password"]);

        try {
            DB::connection()->getPDO();
            echo 'Подключено к:'.DB::connection()->getDatabaseName().PHP_EOL;
            return 0;
        } catch (\Exception $e) {
            echo 'Ошибка подключения' . PHP_EOL . $e->getMessage().PHP_EOL;
            return 1;
        }

        
    }
}
