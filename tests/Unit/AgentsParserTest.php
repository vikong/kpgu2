<?php

namespace Tests\Unit;

use App\Service\Parser\AgentsParser;
use DOMNodeList;
use App\Service\Parser\ProcessInformationParser;
use App\Service\XmlNamespace;
use App\Service\XmlReader;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

use App\Models;



class AgentsParserTest extends TestCase
{
    /**
     * Тест парсера узла Агентов
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
        $parser = new AgentsParser($xml);
        /** @var XmlNamespace $ns */
        foreach ($parser->namespaces() as $ns) {
            $xml->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }

        // получаем узел exc005:АгентыСобытия
        $agents = $xml->getNode('doc:ДанныеДокумента/doc:СообщенияДокумента/exc005:ВыдачаПоручения/exc005:АгентыСобытия', $root);
        
        // получаем тестовый узел exc005:АгентОтправитель
        $agent = $xml->getNode('exc005:АгентОтправитель', $agents);
        $aItems = $parser->parse($agent, 'exc005:Инициатор');
        $test_arr = array(
            "title" => "Инициатор",
            "owner_type" => "Инициатор",
            "agent_reference" => "6d8c1ef5-a5ea-4dd9-a97d-5ee80f0663b1",
            "view" => "Аппарат Правительства РФ"
        );
        $this->assertEquals($test_arr, $aItems);

        // получаем тестовый узел exc005:АгентыПолучатели
        $agents = $xml->getNodes('exc005:АгентыПолучатели', $agents);
        $aItems = $parser->parse($agents->item(0), 'exc005:Исполнитель');
        $test_arr = array(
            "title" => "Исполнитель",
            "owner_type" => "Исполнитель",
            "agent_reference" => "9885bc8f-6987-430a-a644-2321425ddccc",
            "view" => "Минздрав России"
        );
        $this->assertEquals($test_arr, $aItems);

    }
}
