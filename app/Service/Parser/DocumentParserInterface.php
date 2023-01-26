<?php

declare(strict_types=1);

namespace App\Service\Parser;

use DOMElement;
use DOMNode;
use App\Service\XmlNamespace;

/**
 * Interface DocumentParserInterface.
 */
interface DocumentParserInterface
{
    /**
     * @return XmlNamespace[]
     */
    public function namespaces(): array;

    /**
     * @param DOMElement|DOMNode $root
     * @param string $query
     *
     * @return array|null
     */
    public function parse($root, $query=''): ?array;
}
