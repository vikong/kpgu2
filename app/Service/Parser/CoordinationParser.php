<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlReader;
use App\Service\XmlNamespace;
use App\Service\Parser\DocumentParserInterface;

/**
 * Class CoordinationParser 
 * Парсер ДанныхКоординации
 */
class CoordinationParser extends EntityXmlParser implements DocumentParserInterface
{
  /**
   * @param \DOMElement|\DOMNode $root
   * @param string $query
   *
   * @return array|null
   */
  public function parse($root, $query = ''): ?array
  {

    $context = empty($query) ? $root : $this->xmlReader->getNode($query, $root);
    if (!isset($context)) {
      return null;
    }
    $aProp = array();
    //echo $context->getNodePath();
    //$aProp["coordination_data"]=$context->nodeValue;
    /** 
     * @var \DOMElement $node 
     */
    foreach ($context->childNodes as $key => $node) {
      $fld = array();
      if ($node->nodeType == XML_ELEMENT_NODE) {
        $fld["name"] = $aProp[$key] = $node->localName;
        if ($node->hasChildNodes() && $node->childNodes->count() > 1) {
          $fld["value"] = null;
          $fld["json"] = $this->xmlReader->ToJson($node);
          $fld["data_type"] = 'json';
        } else {
          $fld["value"] = $node->nodeValue;
          $fld["json"] = null;
          //todo: определять тип
          $fld["data_type"] = "string";
        }

      }
      //if (count($fld)==0){
      $aProp[$key] = $fld;
      //}         

    }

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