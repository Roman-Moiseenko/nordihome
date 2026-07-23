<?php

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Slug;

class TagEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }
    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }
    public ?string $image_url = null {
        get => $this->image_url;
        set => $this->image_url = $value;
    }
    public bool $isMain  = false {
        get => $this->isMain;
        set => $this->isMain = $value;
    }

    public function __construct(
        string $name,
        Slug $slug,
    ) {
        $this->name = $name;
        $this->slug = $slug;
    }
}
