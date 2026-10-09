<?php

namespace App\Modules\Catalog\Domain\Exceptions;

final class TooManyModificationAttributesException extends ModificationException
{
    public function __construct(int $max)
    {
        parent::__construct("Modification cannot have more than {$max} attributes.");
    }
}
