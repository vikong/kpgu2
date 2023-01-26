<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\DbService;
use App\Service\XmlReader;
use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;

/**
 * Class ItemParser 
 * Парсер Информации о процессе
 */
class ItemParser extends EntityXmlParser implements DocumentParserInterface
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
          "item_state"=>[XmlReader::QUERY=>'СостояниеПредмета', XmlReader::IGNORE_NS=>true],
          "owner_type"=>[XmlReader::QUERY=>'ВидВладельца', XmlReader::ATTR=>'ВидНазвание', XmlReader::IGNORE_NS=>true],
        ];

      $aProp = $this->xmlReader->extractValues($fields, $context);
        
      $coordinationParser = new CoordinationParser($this->xmlReader);
      
      $aProp[DbService::Coordination] = $coordinationParser->parse($context, "./*[local-name() = 'ДанныеДляКоординации']");

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
