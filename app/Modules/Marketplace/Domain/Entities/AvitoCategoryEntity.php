<?php

namespace App\Modules\Marketplace\Domain\Entities;

final class AvitoCategoryEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

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

    /** @var AvitoCategoryEntity[] */
    public array $children = [] {
        get => $this->children;
    }

    public function __construct(
        string $name,
        string $slug,
        ?int $parentId = null,
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->parentId = $parentId;
    }
}
