<?php

namespace Tests\Unit;

use DOMNodeList;
use App\Service\Parser\ProcessInformationParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Models;



class ProcessInformationParserTest extends TestCase
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
        $parser = new ProcessInformationParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }

        // получаем тестовый узел ИнформацияОПроцессе
        $items = $xml->getNodes('doc:ДанныеДокумента/doc:СообщенияДокумента/exc005:ВыдачаПоручения', $root) ;

        $item = $xml->getNode('exc005:ИнформацияОПроцессе',$items->item(0));
        
        $fld = $parser->parse($item);

        $test_arr = array(
            "title"=>"Работа с поручением",
            "process_reference"=>"4b4841f4-3913-11ed-a261-0242ac120002",
            "process_presentation"=>"Цифровое поручение",
            "creator_reference"=>"6d8c1ef5-a5ea-4dd9-a97d-5ee80f0663b1",
            "creator_presentation"=>"Аппарат Правительства РФ",
            "creation_time"=>"2021-12-01T09:59:47+03:00");


        $this->assertEquals($test_arr, $fld);


    }
}
