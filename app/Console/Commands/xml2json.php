<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DOMNodeList;
use App\Service\Parser\ItemParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;


class xml2json extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:xml2json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        echo 'xml2json'.PHP_EOL.PHP_EOL;
        // загружаем тестовый док
        $xml = new XmlReader();
        $xml->loadXml(file_get_contents('./tests/Resources/digital1.xml'));
        //$simpleXml = simplexml_load_file(file_get_contents('./tests/Resources/digital1.xml'));
        

        $root = $xml->getXpath()->document->documentElement;

        // парсер
        $parser = new ItemParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }

        // получаем тестовый узел
        $items = $xml->getNodes('doc:ДанныеДокумента/doc:СообщенияДокумента/exc005:ВыдачаПоручения/exc005:ПредметыСобытия', $root) ;
        
        $items1 = $xml->getNodes('exc005:Поручение/exc005:ДанныеДляКоординации',$items->item(0));
        //$items1 = $xml->getNodes('exc005:Сотрудники/exc005:ДанныеДляКоординации',$items->item(0));
        //$items1 = $xml->getNodes('exc005:Соисполнение/exc005:ДанныеДляКоординации',$items->item(0));
        $node = $items1->item(0);
        //$node->normalize();
        $json = $xml->ToJson($node);
        echo PHP_EOL.'json:::' . PHP_EOL. PHP_EOL;
        echo $json.PHP_EOL;
        file_put_contents('../_xml2json.json', $json);

        //echo preg_replace('/\s{2,}/','', preg_replace('/[ \t]+/', '', $node->nodeValue)).PHP_EOL;

        //var_dump(preg_replace('/\s/', '', $node->textContent) );

        return 0;
    }
}
