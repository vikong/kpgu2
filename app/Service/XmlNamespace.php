<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Class XmlNamespace 
 * Информация о пространстве имён xml
 */
class XmlNamespace
{
    /**
     * @var string
     */
    public $prefix;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @param string $prefix
     * @param string $namespace
     */
    public function __construct($prefix, $namespace)
    {
        $this->prefix = $prefix;
        $this->namespace = $namespace;
    }
}