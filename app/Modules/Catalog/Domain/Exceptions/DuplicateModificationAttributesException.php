<?php

namespace App\Modules\Catalog\Domain\Exceptions;

final class DuplicateModificationAttributesException extends ModificationException
{
    public function __construct(int $attributeId)
    {
        parent::__construct("Attribute #{$attributeId} is duplicated in the modification.");
    }
}
