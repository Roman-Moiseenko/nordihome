<?php

declare(strict_types=1);

namespace App\Modules\Content\Domain\Entities;

use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Shared\Domain\ValueObjects\Slug;

final class ContactEntity
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

    public ?ContactChannel $channel = null {
        get => $this->channel;
        set => $this->channel = $value;
    }

    public ?string $svg = null {
        get => $this->svg;
        set => $this->svg = $value;
    }

    public string $icon = '' {
        get => $this->icon;
        set => $this->icon = $value;
    }

    public string $color = '#000000' {
        get => $this->color;
        set => $this->color = $value;
    }

    public string $url = '' {
        get => $this->url;
        set => $this->url = $value;
    }

    public int $sort = 0 {
        get => $this->sort;
        set => $this->sort = $value;
    }

    public bool $published = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public function __construct(
        string $name,
        Slug   $slug,
        string $icon = '',
        string $color = '#000000',
        string $url = '',
        ?ContactChannel $channel = null,
        ?string $svg = null,
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->icon = $icon;
        $this->color = $color;
        $this->url = $url;
        $this->channel = $channel;
        $this->svg = $svg;
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
