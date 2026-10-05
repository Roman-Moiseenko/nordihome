<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

final class GroupEntity
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

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    public bool $published = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public ?Meta $meta = null {
        get => $this->meta;
        set => $this->meta = $value;
    }

    public function __construct(
        string $name,
        Slug $slug,
    ) {
        $this->name = $name;
        $this->slug = $slug;
    }

    public function publish(): void
    {
        $this->published = true;
    }

    public function unpublish(): void
    {
        $this->published = false;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
