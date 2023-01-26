<?php

namespace Tests\Unit;

use App\Service\Parser\EventInformationParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use PHPUnit\Framework\TestCase;


class EventInformationTest extends TestCase
{
    /**
     * A basic test Service/Parser/EventInformationParser.php
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
        $parser = new EventInformationParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }

        // получаем тестовый узел
        $items = $xml->getNodes('doc:ДанныеДокумента/doc:СообщенияДокумента/exc005:ВыдачаПоручения', $root) ;
        $item = $xml->getNode('exc005:ИнформацияОСобытии',$items->item(0));
        $fld = $parser->parse($item);
        var_dump($fld);

        $test_arr = array(
            "title"=>"Выдача поручения",
            "state"=>"Инициация",
            "event_reference"=>"d5db9121-8aba-4595-b28f-5991bfca7bf8",
            "event_presentation"=>"Событие Выдача поручения по поручению",
            "event_time"=>"2021-12-01T09:59:47+03:00"
        );
        $this->assertEquals($test_arr, $fld);

    }
}
