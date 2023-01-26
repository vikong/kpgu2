<?php

namespace Tests\Unit;

use DOMNodeList;
use App\Service\Parser\ItemParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Models;

class XmlToJsonParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        // загружаем тестовый док
        $xml = new XmlReader();
        $xml->loadXml(file_get_contents('./tests/Resources/digital1.xml'));
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
        $node = $xml->getNode('exc005:ПунктПоручения',$items1->item(0));
        var_dump($node);
    }
}
