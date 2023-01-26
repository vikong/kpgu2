<?php

namespace Tests\Unit;

use App\Service\Parser\ItemListParser;
use DOMNodeList;
use App\Service\Parser\ItemParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Service\Parser\DocumentTitleParser;

use App\Models;

class TitleDocTest extends TestCase
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
        $parser = new DocumentTitleParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }
      

        // получаем тестовый узел
        $items = $xml->getNode('doc:ДанныеДокумента', $root) ;

        $aItems = $parser->parse($items,'doc:ЗаголовокДокумента');

        $this->assertEquals(6, count($aItems));
        
        $test_arr = array(
            "title"=>"Резолюция",
            "document_reference"=>"bc5a2f6a-901e-476e-a12c-24b84b7cfcff",
            "document_presentation"=>"Резолюция",
            "creator_reference"=>"6d8c1ef5-a5ea-4dd9-a97d-5ee80f0663b1",
            "creator_presentation"=>"Аппарат Правительства РФ",
            "creation_time"=>"2021-12-01T09:59:47+03:00");
       $this->assertEquals($test_arr, $aItems);
}
}

