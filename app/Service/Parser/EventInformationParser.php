<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlReader;
use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;

/**
 * Class EventInformationParser
 * Парсер Информации о событии
 */
class EventInformationParser extends EntityXmlParser implements DocumentParserInterface
{
    /**
     * @param \DOMElement|\DOMNode $root
     * @param string $query
     *
     * @return array
     */
    public function parse($root, $query=''): array
    {

        $context = empty($query) ? $root : $this->xmlReader->getNode($query, $root);

        $fields = [
            "title"=>[XmlReader::ATTR=>'ВидНазвание', XmlReader::IGNORE_NS=>true],
            "state"=>[XmlReader::QUERY=>'СтатусСобытия', XmlReader::IGNORE_NS=>true],
            "event_reference"=>[XmlReader::QUERY=>'cdm:СсылкаСобытия', XmlReader::ATTR=>'cdm:СобытиеУУИД'],
            "event_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаСобытия', XmlReader::ATTR=>'cdm:Представление'],
            "event_time"=>[XmlReader::QUERY=>'cdm:ВремяСобытия']
        ];

        $aProp = $this->xmlReader->extractValues($fields, $context);

        return $aProp;
    }

    /**
     * @return XmlNamespace[]
     */
    public function namespaces(): array
    {
        return array();
    }
}
