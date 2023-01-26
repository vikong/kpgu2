<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlReader;
use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;

/**
 * Class ProcessInformationParser 
 * Парсер Информации о процессе
 */
class ProcessInformationParser extends EntityXmlParser implements DocumentParserInterface
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
          "title"=>[XmlReader::ATTR=>"ВидНазвание", XmlReader::IGNORE_NS=>true],
          "process_reference"=>[XmlReader::QUERY=>'СсылкаПроцесса', XmlReader::ATTR=>'ПроцессУУИД', XmlReader::IGNORE_NS=>true],
          "process_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаПроцесса', XmlReader::ATTR=>'cdm:Представление'],
          "creator_reference"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:АгентУУИД'],
          "creator_presentation"=>[XmlReader::QUERY=>'cdm:СсылкаСоздателя', XmlReader::ATTR=>'cdm:Представление'],
          "creation_time"=>[XmlReader::QUERY=>'cdm:ВремяСоздания']
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
