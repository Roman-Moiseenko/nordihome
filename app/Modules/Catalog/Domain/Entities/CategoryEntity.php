<?php

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

final class CategoryEntity
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
    public ?string $icon_url = null {
        get => $this->icon_url;
        set => $this->icon_url = $value;
    }
    public ?string $svgIcon = null {
        get => $this->svgIcon;
        set => $this->svgIcon = $value;
    }

    public bool $published  = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public ?Meta $meta  = null {
        get => $this->meta;
        set => $this->meta = $value;
    }
    // ======================== Иерархия (Nested Set) ========================

    public ?int $parentId = null {
        get => $this->parentId;
        set => $this->parentId = $value;
    }

    public int $left = 0 {
        get => $this->left;
        set => $this->left = $value;
    }

    public int $right = 0 {
        get => $this->right;
        set => $this->right = $value;
    }

    public int $depth = 0 {
        get => $this->depth;
        set => $this->depth = $value;
    }

    // ======================== Связи (заполняются репозиторием) ========================

    /* TODO Добавить в будущем
     @var TextParameter[]
    public array $textParameters = [] {
        get => $this->textParameters;
    }
*/
    /** @var CategoryEntity[] */
    public array $children = [] {
        get => $this->children;
    }

    public function __construct(
        string $name,
        Slug $slug,
        ?int $parentId = null
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->parentId = $parentId;
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
