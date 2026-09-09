<?php

namespace App\Modules\Content\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Slug;

class LabelEntity
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

    public function __construct(
        string $name,
        Slug $slug,
    ) {
        $this->name = $name;
        $this->slug = $slug;
    }
}
