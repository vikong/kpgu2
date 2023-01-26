<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\Service\XmlReader;

/**
 * Class EntityXmlParser 
 * шаблон парсера
 */
class EntityXmlParser 
{
    /**
     * @var XmlReader
     */
    protected $xmlReader;

    /**
     * @param XmlReader $xmlReader
     */
    public function __construct($xmlReader)
    {
      $this->xmlReader = $xmlReader;
    }

    /**
	 * @return array
	 */
	public function namespaces(): array
  {
    return [];
	}
}
