<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

final class AttributeGroupEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?string $svg = null {
        get => $this->svg;
        set => $this->svg = $value;
    }

    public int $sort = 0 {
        get => $this->sort;
        set => $this->sort = $value;
    }

    public function __construct(
        string $name,
        ?string $svg = null,
    ) {
        $this->name = $name;
        $this->svg = $svg;
    }
}
