<?php

namespace Tests\Unit;

use DOMNodeList;
use App\Service\Parser\ItemParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Models;

class ItemParserTest extends TestCase
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
        
        $item = $xml->getNode('exc005:Поручение',$items->item(0));
        $fld = $parser->parse($item);
        //var_dump($fld);
        //todo: заполнение тестового массива
        $test_arr = array(
            "title" => "Поручение",
            "item_state" => "Выдано",
            "owner_type" => "Инициатор",
            "coordination" => array()
        );
        //$this->assertEquals($test_arr, $fld);
        $this->assertEquals($test_arr["title"], $fld["title"]);

        // 
        $item = $xml->getNode('exc005:Сотрудники',$items->item(1));
        $fld = $parser->parse($item);
        //var_dump($fld);

        $this->assertEquals('Сотрудник', $fld["title"]);

    }
}
