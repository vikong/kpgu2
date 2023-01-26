<?php

namespace App\Service;

use DOMDocument;
use DOMNodeList;

class XmlContainer
{
    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * Валидация XML по схеме XSD
     *
     * @param string $filename
     * @param string $schemaFilename
     *
     * @return array
     */
    public function loadXmlWithSchema(string $schema, string $receiptContent): array
    {
        libxml_use_internal_errors(true);
        $this->dom = new DOMDocument();
        
        $this->dom->loadXML($receiptContent);
        return [
            'is_valid'          => $this->dom->schemaValidateSource($schema),
            'validation_errors' => libxml_get_errors() ?? null,
        ];
    }

    public function getNode(string $xmlPath): DOMNodeList
    {
        return $this->dom->getElementsByTagName($xmlPath);
        //return $this->dom->childNodes;
    }
    
    public function loadXml(string $receiptContent): bool
    {
        libxml_use_internal_errors(true);
        $this->dom = new DOMDocument();
        $this->dom->loadXML($receiptContent);
        return true;
    }

}
