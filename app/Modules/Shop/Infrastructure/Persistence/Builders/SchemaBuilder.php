<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Builders;

use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;

class SchemaBuilder
{

    public function createSchema(): SchemaData
    {
        return new SchemaData();
    }
}
