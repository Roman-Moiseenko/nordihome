<?php

declare(strict_types=1);

namespace App\Modules\Order\Domain\Exceptions;

final class ClientOrderDataIncompleteException extends \DomainException
{
    /** @param string[] $missingFields */
    public function __construct(public readonly array $missingFields)
    {
        parent::__construct('Client data incomplete: ' . implode(', ', $missingFields));
    }
}
