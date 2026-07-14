<?php

namespace App\Modules\Shop\Domain\Schema;

interface SchemaElement
{
    public function toArray(): array;
}
