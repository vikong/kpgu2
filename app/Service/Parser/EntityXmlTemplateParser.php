<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;
use App\Service\XmlReader;

/**
 * Class EntityXmlTemplateParser 
 * шаблон парсера
 */
class EntityXmlTemplateParser extends EntityXmlParser implements DocumentParserInterface
{
    /**
     * @return array
     */
    public function namespaces(): array
    {
      return array();
    }

    /**
     * @param \DOMElement $root
     * @param string $query
     *
     * @return array
     */
    public function parse($root, $query=''): array
    {

      $context = empty($query) ? $root : $this->xmlReader->getNode($query, $root);
      
      // вариант 1

      $fields = [
        "title"=>[XmlReader::ATTR=>'doc:ВидНазвание'],
        "document_reference"=>[XmlReader::QUERY=>'cdm:СсылкаДокумента', XmlReader::ATTR=>'cdm:ДокументУУИД'],
        "document_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаДокумента', XmlReader::ATTR=>'cdm:Представление'],
        "creator_reference"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:АгентУУИД'],
        "creator_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:Представление'],
        "creation_time"=>[XmlReader::QUERY=>'cdm:ВремяСоздания'],
    ];

    $aProp = $this->xmlReader->extractValues($fields, $context);

        // вариант 2
        /*
        $aProp = array();

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

}
