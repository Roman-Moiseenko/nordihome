<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Elements;

final readonly class ChildrenData
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $slug,
    )
    {
    }
}
