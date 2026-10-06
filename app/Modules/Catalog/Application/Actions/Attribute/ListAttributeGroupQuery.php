<?php

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\ListAttributeTypeData;
use App\Modules\Catalog\Domain\ValueObjects\AttributeType;

class ListAttributeGroupQuery
{

    public function execute()
    {
        $list = [];

        foreach (AttributeType::ATTRIBUTES as $key => $value) {
            $list[] = new ListAttributeTypeData(
                value: $key,
                label: $value,
                isVariant: $key === AttributeType::TYPE_VARIANT
            );
        }

        return $list;
    }
}
