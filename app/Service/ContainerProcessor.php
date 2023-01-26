<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Parser\AgentsParser;
use App\Service\Parser\DocumentTitleParser;
use App\Service\Parser\EventInformationParser;
use App\Service\Parser\ItemListParser;
use App\Service\Parser\ProcessInformationParser;
use App\Service\XmlReader;
use ErrorException;

/**
 * Class ContainerProcessor 
 * Обработчик xml контейнера
 */
class ContainerProcessor
{
    /**
     * @var XmlReader
     */
    private $xmlReader;

    /**
     * @var \DOMElement;
     */
    private $root;

    private $namespaces;

    public function __construct()
    {
        $this->xmlReader = new XmlReader();
    }

    /**
     * Загружает рабочий контейнер
     * @param string $file файл контейнера
     */
    public function loadContainer(string $file)
    {
        // конвертируем, если файл в неопознанной кодировке
        $xmlString = iconv('UTF-8', 'UTF-8//IGNORE', file_get_contents($file));
        $this->xmlReader->loadXml($xmlString);
        $this->root = $this->xmlReader->getXpath()->document->documentElement;
        $this->namespaces = array(
            new XmlNamespace("exc005", "urn:Exc01-005-00001:ExchangeMeta:v0.2.3"),
            new XmlNamespace("exc001", "urn:Exc01-T001-00001:ExchangeMeta:v0.1.0"),
            new XmlNamespace("sdm005", "urn:Adm01-005:SubjectDomainMeta:v0.1.0"),
            new XmlNamespace("sdm004", "urn:Adm01-004:SubjectDomainMeta:v0.1.0"),
        );

    }

	/**
	 * @return XmlNamespace[]
	 */
    public function GetNameSpaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Преобразует в массив узел ЗаголовокДокумента рабочего контейнера
     * @return array
     */
    public function extractDocumentTitle(): array
    {
        $parser = new DocumentTitleParser($this->xmlReader);
        $node = $this->xmlReader->getNode('doc:ДанныеДокумента/doc:ЗаголовокДокумента', $this->root);
        $fields = $parser->parse($node);
        return $fields;
    }

    /**
     * Преобразует в массив узел СобытияДокумента рабочего контейнера
     * @throws ErrorException 
     * @return array
     */
    public function extractEvents(): array
    {
        // события документа
        $aEvents=array();

        /** @var XmlNamespace $ns */
        foreach ($this->GetNameSpaces() as $ns) {
            $this->xmlReader->getXpath()->registerNamespace($ns->prefix, $ns->namespace);
        }
        $itemParser = new ItemListParser($this->xmlReader);
        $eventInfoParser = new EventInformationParser($this->xmlReader);

        // Сообщения документа могут быть из разных namespace, поэтому сканируем все childNodes
        $eventsNode = $this->xmlReader->getNode('doc:ДанныеДокумента/doc:СообщенияДокумента', $this->root);
        if($eventsNode->hasChildNodes()) {
            $processParser = new ProcessInformationParser($this->xmlReader);
            $agentsParser = new AgentsParser($this->xmlReader);
    
            foreach ($eventsNode->childNodes as $key => $eventNode) {
                if ($eventNode->nodeType==XML_ELEMENT_NODE)
                {
                    // события
                    $event = array();

                    $event["title"] = $eventNode->localName;

                    //echo '*ИнформацияОПроцессе'.PHP_EOL;
                    $event[DbService::ProcessInformation] = 
                      $processParser->parse($eventNode, "./*[local-name() = 'ИнформацияОПроцессе']");

                    //echo 'ИнформацияОСобытии'.PHP_EOL;
                    $event[DbService::EventInformation] = 
                      $eventInfoParser->parse($eventNode, "./*[local-name() = 'ИнформацияОСобытии']");

                    //echo 'Агенты'.PHP_EOL;
                    $agentsNode = $this->xmlReader->getNode("./*[local-name() = 'АгентыСобытия']", $eventNode);
                    
                    // Агент Отправитель
                    $senderNode = $this->xmlReader->getNode("./*[local-name() = 'АгентОтправитель']", $agentsNode);
                    if($senderNode->hasChildNodes())
                    {
                        foreach ($senderNode->childNodes as $sender) {
                            if($sender->nodeType==XML_ELEMENT_NODE)
                            {
                                $event["sender"] = $agentsParser->parse($sender);
                            }
                        }
                    }
                    if (!isset($event["sender"])){throw new ErrorException("SENDER");}
                    
                    // Получатели
                    $recieversNode = $this->xmlReader->getNode("./*[local-name() = 'АгентыПолучатели']", $agentsNode);
                    
                    $recievers = array();
                    if($recieversNode->hasChildNodes())
                    {
                        foreach($recieversNode->childNodes as $a => $agentNode)
                        {
                            if($agentNode->nodeType==XML_ELEMENT_NODE)
                            {
                                $recievers[$a] = $agentsParser->parse($agentNode);
                            }
    
                        }
                    }
                    $event["recievers"] = $recievers; 

                    //echo 'ПредметыСобытия'.PHP_EOL;
                    $fld = $itemParser->parse($eventNode, "./*[local-name() = 'ПредметыСобытия']" );
                    $event["items"] = $fld;
                    //var_dump($fld);

                    $aEvents[$key] = $event;
                    //var_dump($event);
                }
            }
        }

        return $aEvents;
    }


}