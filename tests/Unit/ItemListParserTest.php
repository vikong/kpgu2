<?php

namespace Tests\Unit;

use App\Service\Parser\ItemListParser;
use DOMNodeList;
use App\Service\Parser\ItemParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Models;

class ItemListParserTest extends TestCase
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
        $parser = new ItemListParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }

        // получаем тестовый узел
        $items = $xml->getNode('doc:ДанныеДокумента/doc:СообщенияДокумента/exc005:ВыдачаПоручения', $root) ;
        
        $aItems = $parser->parse($items,'exc005:ПредметыСобытия');
        //var_dump($aItems);

        $this->assertEquals(3, count($aItems));

        // 
        //$item = $xml->getNode('exc005:Сотрудники',$items->item(1));
        //$fld = $parser->parse($item);
        //var_dump($fld);

        //$this->assertEquals('Сотрудник', $fld["title"]);

    }
}
