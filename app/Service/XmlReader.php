<?php
declare(strict_types=1);

namespace App\Service;

use DOMDocument;
use DOMElement;
use DOMNamedNodeMap;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Arr;

/**
 * Class XmlReader
 */
class XmlReader
{
    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * Загружает строку xml
     * @param string $xmlString
     * @param bool $clearNamespaces
     */
    public function loadXml($xmlString, $clearNamespaces=false)
    {
        //$doc = new DOMDocument('1.0', 'UTF-8');
        $doc = new DOMDocument('2.0', 'UTF-8');
        @$doc->loadXML($xmlString);
        $this->loadDom($doc, $clearNamespaces);
    }

    /**
     * Загружает xml DOM
     * @param DOMDocument $document
     * @param bool $clearNamespaces
     */
    public function loadDom(DOMDocument $document, $clearNamespaces=false)
    {
        $this->dom=$document;
        $this->xpath = new DOMXPath($document);
        if ($clearNamespaces) {
            $this->clearNamespaces();
        }
    }

    /**
     * @return DOMXPath
     */
    public function getXpath(): DOMXPath
    {
        return $this->xpath;
    }

    public function GetNamespaces(DOMNode $context=null)
    {
        if(!isset($context)){
            $context = $this->getXpath()->document->documentElement;
        }
        //$res = array();
        foreach ($this->xpath->query('//namespace::*', $context) as $namespaceNode) {
            $prefix = str_replace('xmlns:', '', $namespaceNode->nodeName);
            echo $prefix.' '.$namespaceNode->nodeValue, "\n";
            //$nodes  = $this->xpath->query("//*[namespace::{$prefix}]", $context);
        }
    }

    /**
     * Очищает Xml NameSpaces
     * @return void
     */
    public function clearNamespaces()
    {
        /** @var DOMNode $namespaceNode */
        foreach ($this->xpath->query('//namespace::*') as $namespaceNode) 
        {
            $prefix = str_replace('xmlns:', '', $namespaceNode->nodeName);
            $nodes  = $this->xpath->query("//*[namespace::{$prefix}]");
            /** @var DOMElement $node */
            foreach ($nodes as $node) 
            {
                $namespaceUri = $node->lookupNamespaceURI($prefix);
                $node->removeAttributeNS($namespaceUri, $prefix);
            }
        }
        $this->loadXml($this->dom->saveXML(), false);        
    }

    /**
     * Возвращает представление узла в виде массива
     * @param DOMNode|null $node
     * @return array
     */
    public function getNodeXPath(DOMNode $node) : array
    {
        $res = array();

        if($node->nodeType == XML_TEXT_NODE){
            $res = $node->nodeValue;
        }
        else{
            if($node->hasAttributes()) {
                $attributes = $node->attributes;
                if(!is_null($attributes)){
                    $res['@attributes'] = array();
                    foreach ($attributes as $index=>$attr) {
                        $res['@attributes'][$attr->name] = $attr->value;
                    }
                }
            }
            if($node->hasChildNodes()) {
                $children = $node->childNodes;
                for($i=0;$i<$children->length;$i++){
                    $child = $children->item($i);
                    $res[$child->nodeName] = $this->getNodeXPath($child);
                }
            }
        }

        return $res;
    }

    /**
     * Возвращает значение узла по пути
     * @param string $query                 Query xpath
     * @param DomNode|null $context        Context node
     * @param string|null $def                   Default Value
     * @return string
     */
    public function getValue($query, DOMNode $context = null, ?string $def = ''): ?string
    {
        $nodes = $this->xpath->query($query, $context);
        if ($nodes->length == 0) {
            return $def;
        }

        return $nodes->item(0)->nodeValue;
    }

    /**
     * Возвращает значение атрибута по пути
     * @param string $query                 Путь xpath
     * @param string $attr          Атрибут
     * @param DomNode|null $context        Context node
     * @param string|null $def                   Default Value
     * @return string
     */
    public function getAttribute($query, $attr, DOMNode $context = null, ?string $def = ''): ?string
    {
        $nodes = $this->xpath->query($query, $context);
        if ($nodes->length == 0) {
            return $def;
        }

        return $nodes->item(0)->getAttribute($attr);
    }

    /**
     * Возвращает узел по пути
     * @param string        $query
     * @param DOMNode|null $context
     * @return DOMElement|null
     */
    public function getNode($query, $context): ?DOMElement
    {
        $nodes = $this->xpath->query($query, $context);
        if ($nodes->length == 0) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    /**
     * Возвращает список узлов по пути
     * @param string        $query
     * @param DOMNode|null $context
     * @return DOMNodeList
     */
    public function getNodes($query, $context): DOMNodeList
    {
        return $this->xpath->query($query, $context);
    }
    
    /**
     * Конвертируе DOMNode в DOMElement
     * @param DOMNode $node
     */
    public function cast_e(DOMNode $node) : ?DOMElement 
    {
        if ($node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                return $node;
            }
        }
        return null;
    }

    const QUERY = "query";
    const ATTR = "attr";
    const IGNORE_NS = "ns_ignore";

    /**
     * формирует массив значений для указанных узлов и атрибутов
     * @param array        $aQuery
     * @param DOMNode $context
     * @return array
     */
    public function extractValues(array $aQuery, DOMNode $context): array
    {
        $aProp=array();

        foreach ($aQuery as $key => $q) {
            if (isset($q[self::IGNORE_NS]) && $q[self::IGNORE_NS]) {

                if (isset($q[self::ATTR])) {
                    $query = "@*[local-name() = '${q[self::ATTR]}']";
                    if (isset($q[self::QUERY])) {
                        $query = "./*[local-name() = '${q[self::QUERY]}']/${query}";
                    } else {
                        $query = "./${query}";
                    }
                } else {
                    $query=$query = "./*[local-name() = '${q[self::QUERY]}']";
                }
                $aProp[$key] = $this->getValue($query, $context);

            } else {

                if (isset($q[self::ATTR])) {
                    if (isset($q[self::QUERY])) {
                        $aProp[$key] = $this->getAttribute($q[self::QUERY], $q[self::ATTR], $context);
                    } else {
                        $aProp[$key] = $this->cast_e($context)->getAttribute($q[self::ATTR]);
                    }
                } else {
                    $aProp[$key] = $this->getValue($q[self::QUERY], $context);
                }
            }

        }

        return $aProp;

    }

    public function ToString()
    {
        return $this->dom->saveXML();
    }

    /**
     * Возвращает представление узла в виде json
     * @param DOMNode $node
     * @param int $level
     * @return string
     */
    public function ToJson(DOMNode $node, int $level=0 ): string
    {
        if($node->nodeType == XML_TEXT_NODE ){
            $nodeVal=$node->nodeValue;
            if (!isset($nodeVal) || strlen(trim($nodeVal)) ==0 ){
                return '';
            }
            return '"'.$nodeVal.'"';
        }

        $complex = false;
        $hasAttributes = false;
        $attr = '';
        if($node->hasAttributes()) {
            $attributes = $node->attributes;
            if(!is_null($attributes)){
                $hasAttributes = true;
                $complex = true;
                $attr = $this->AttributesToJson($attributes);
            }
        }

        $inner = '';
        if($node->hasChildNodes()) {
            $children = $node->childNodes;
            $nodeCount = 0;
            if ($children->length==1 && $children->item(0)->nodeType==XML_TEXT_NODE && $children->item(0)->nodeName=='#text')
            {
                $inner = '"'.$node->localName.'":'.$this->FormatNodeValue($children->item(0));
            } else {
                for ($i = 0; $i < $children->length; $i++) {
                    $child = $children->item($i);
                    $childJson = $this->ToJson($child, $level+1);
                    if (strlen($childJson) > 0) {
                        $inner = $inner.($nodeCount>0? ','.PHP_EOL:'') . $childJson;
                        $nodeCount++;
                    }
                }
                $complex = true;
                if ($nodeCount>0) {
                    $inner = $inner . PHP_EOL;
                }
            }
        } 
        else 
        {
            $inner = '"'.$node->localName.'":"'.$node->nodeValue.'"';
        }

        $ident = str_repeat('  ', $level);

        if ($complex){
            return $ident.($level>0? '"'.$node->localName.'":' : '' ).'{'.PHP_EOL
                .$ident.($hasAttributes? '"Атрибуты":{'.$attr.'}, '.PHP_EOL.$ident : '').$inner.$ident.'}';
        }
        else{
            return $ident.$inner;
        }

        
    }

    /**
     * Возвращает представление атрибутов в виде json
     * @param DOMNamedNodeMap $attributeNode
     * @return string
     */
    public function AttributesToJson(DOMNamedNodeMap $attributeNode): string
    {

        $res = '';
        foreach ($attributeNode as $index=>$attr) {
            $res = $res.'"'.$attr->name.'":"'.$attr->value.'", ';
        }
        return rtrim($res, ", ");
    }

    /**
     * Возвращает значение узла в формате json 
     * @param DOMNode $node
     * @return float|int|string
     */
    public function FormatNodeValue(DOMNode $node )
    {
        $value=$node->nodeValue;
        if(is_numeric($value)){
            return $value;
        }
        return '"' . $value . '"';
    }

}