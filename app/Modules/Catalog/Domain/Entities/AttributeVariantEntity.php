<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

final class AttributeVariantEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $attributeId = null {
        get => $this->attributeId;
        set => $this->attributeId = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public ?string $slug = null {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public function __construct(
        string $name,
        ?string $slug = null,
    ) {
        $this->name = $name;
        $this->slug = $slug;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}
