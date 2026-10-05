<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

final class EquivalentEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?int $categoryId = null {
        get => $this->categoryId;
        set => $this->categoryId = $value;
    }

    public function __construct(
        string $name,
        ?int $categoryId = null,
    ) {
        $this->name = $name;
        $this->categoryId = $categoryId;
    }
}
