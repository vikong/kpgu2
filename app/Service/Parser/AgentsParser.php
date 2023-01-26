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
class AgentsParser extends EntityXmlParser implements DocumentParserInterface
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
        "owner_type"=>[XmlReader::ATTR=>'ВидНазвание', XmlReader::IGNORE_NS=>true],
        "agent_reference"=>[XmlReader::QUERY=>'cdm:СсылкаАгента', XmlReader::ATTR=>'cdm:АгентУУИД'],
        "view"=>[XmlReader::QUERY=>'cdm:СсылкаАгента', XmlReader::ATTR=>'cdm:Представление']
      ];

      $aProp = $this->xmlReader->extractValues($fields, $context);
      $aProp["title"] = $context->localName;
        
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
