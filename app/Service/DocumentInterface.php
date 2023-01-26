<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Interface DocumentInterface.
 */
interface DocumentInterface
{
    /**
     * Get Name for Document.
     *
     * @return string
     */
    public function getName(): string;
    public function getFields(): array;
}
