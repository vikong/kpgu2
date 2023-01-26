<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlReader;
use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;
use Arr;

/**
 * Class ItemListParser 
 * Парсер Предметов
 */
class ItemListParser extends EntityXmlParser implements DocumentParserInterface
{
    /**
     * @param \DOMElement|\DOMNode $root
     * @param string $query
     *
     * @return array
     */
    public function parse($root, $query=''): array
    {
        $aItems = array();
        $context = empty($query) ? $root : $this->xmlReader->getNode($query, $root);
        if($context->hasChildNodes()) {
            $itemParser = new ItemParser($this->xmlReader);
            foreach ($context->childNodes as $key => $item) {
                if ($item->nodeType==XML_ELEMENT_NODE)
                {
                    $fld = $itemParser->parse($item);
                    $aItems[$key] = $fld;
                }
            }
        }        

        return $aItems;
        
    }

    /**
	 * @return XmlNamespace[]
	 */
	public function namespaces(): array
  {
    return array();
	}


}