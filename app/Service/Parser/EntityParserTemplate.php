<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\Parser\DocumentParserInterface;
use App\Service\XmlNamespace;
use App\Service\XmlReader;


/**
 * Class EntityParserTemplate 
 * шаблон парсера
 */
class EntityParserTemplate extends EntityXmlParser implements DocumentParserInterface
{
    /**
     * @param \DOMElement $root
     *
     * @return array
     */
    public function parse($root): array
    {

      $fields = [
        "title"=>[XmlReader::ATTR=>'doc:ВидНазвание'],
        "document_reference"=>[XmlReader::QUERY=>'cdm:СсылкаДокумента', XmlReader::ATTR=>'cdm:ДокументУУИД'],
        "document_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаДокумента', XmlReader::ATTR=>'cdm:Представление'],
        "creator_reference"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:АгентУУИД'],
        "creator_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:Представление'],
        "creation_time"=>[XmlReader::QUERY=>'cdm:ВремяСоздания'],
    ];

    $aProp = $this->xmlReader->extractValues($fields, $root);
        // вариант 2
        /*
        $aProp = array();
        
        $xml = $this->xmlReader;

        $node = $xml->getNode('doc:ДанныеДокумента/doc:ЗаголовокДокумента', $this->rootNode);
        $aProp["title"]=$node->getAttribute('doc:ВидНазвание');

        $aProp["reference_document"] = 
          $xml->getNode('cdm:СсылкаДокумента', $node)->getAttribute('cdm:ДокументУУИД');

        $aProp["presentation_document"] = 
          $xml->getNode('cdm:СсылкаДокумента', $node)->getAttribute('cdm:Представление');
    
        $aProp["creator_link"] =
          $xml->getNode('cdm:СсылкаСоздателя', $node)->getAttribute('cdm:АгентУУИД');
        
        $aProp["creator_presentation"] =
          $xml->getAttribute('cdm:СсылкаСоздателя','cdm:Представление', $node);

        $aProp["creation_time"] =
          $xml->getValue('cdm:ВремяСоздания', $node);
        */
        
        return $aProp;
    }

	/**
	 * @return XmlNamespace[]
	 */
	public function namespaces(): array
  {
    return [];
	}


}
