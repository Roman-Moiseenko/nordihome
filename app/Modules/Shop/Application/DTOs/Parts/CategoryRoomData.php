<?php

namespace App\Modules\Shop\Application\DTOs\Parts;
class CategoryRoomData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $image,

    )
    {
    }

}

