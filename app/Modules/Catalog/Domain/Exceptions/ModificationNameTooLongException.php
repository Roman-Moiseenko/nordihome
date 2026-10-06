<?php

namespace App\Modules\Catalog\Domain\Exceptions;

final class ModificationNameTooLongException extends ModificationException
{
    public function __construct(int $max)
    {
        parent::__construct("Modification name cannot exceed {$max} characters.");
    }
}
