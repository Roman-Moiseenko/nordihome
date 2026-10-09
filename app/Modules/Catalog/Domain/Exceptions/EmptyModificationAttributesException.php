<?php

namespace App\Modules\Catalog\Domain\Exceptions;

final class EmptyModificationAttributesException extends ModificationException
{
    public function __construct()
    {
        parent::__construct('Modification must have at least one attribute.');
    }
}
