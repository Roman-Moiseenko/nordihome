<?php

namespace App\Modules\Catalog\Domain\Exceptions;

final class EmptyModificationNameException extends ModificationException
{
    public function __construct()
    {
        parent::__construct('Modification name cannot be empty.');
    }
}
