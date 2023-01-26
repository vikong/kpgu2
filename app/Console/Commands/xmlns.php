<?php

namespace App\Console\Commands;

use App\Service\XmlReader;
use Illuminate\Console\Command;

class ConfigDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:xmlns';

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
        //$xml->GetNamespaces();
        //$xml->clearNamespaces();
        //echo $xml->ToString();
        $prefix = 'exc005';
        //$xml->getXpath()->registerNamespace($prefix, "urn:IEDMS:OPENDATA");
        $xml->getXpath()->registerNamespace($prefix, "urn:Exc01-005-00001:ExchangeMeta:v0.2.3");
        $node = $xml->getNode('doc:ДанныеДокумента/doc:СообщенияДокумента', $root);
        $query = './ВыдачаПоручения/ПредметыСобытия';
        $query = str_replace("/", "/$prefix:", $query);
        //$items = $xml->getNodes($query, $node);
        
        #$items = $xml->getNodes('exc005:ВыдачаПоручения/exc005:ПредметыСобытия', $node);
        // equivalent to:
        $items = $xml->getNode("*[local-name() = 'ВыдачаПоручения']/*[local-name() = 'ПредметыСобытия']", $node);
        $item1 = $xml->cast_e($items->childNodes->item(1));
        echo $item1->nodeName, PHP_EOL;
        //$item1->removeAttributeNS();
        //$attr = $xml->getNodes("./exc005:Поручение/@exc005:ВидНазвание", $items);
        //$attr = $xml->getNodes("./exc005:Поручение/@exc005:ВидНазвание", $items);
        #$attr = $xml->getNodes("./exc005:Поручение/@*[exc005:ВидНазвание='Поручение']", $items);
        
        //$attr = $xml->getNodes("./@exc005:ВидНазвание", $item1); // получает атрибуты exc005:ВидНазвание
        //$attr = $xml->getNodes("./@*", $item1); // получает все атрибуты
        $attr = $xml->getNodes("./@*[local-name() = 'ВидНазвание']", $item1); // получает атрибуты
        var_dump($attr->item(0));
        //$namespaceUri = $node->lookupNamespaceURI($prefix);
        //var_dump($namespaceUri);
        //$node->removeAttributeNS($namespaceUri, $prefix);
        //var_dump($item1->nodeName);
        //$xml->GetNamespaces($item);
        echo PHP_EOL;

        return 0;
    }
}
