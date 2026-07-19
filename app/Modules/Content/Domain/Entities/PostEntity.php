<?php

declare(strict_types=1);

namespace App\Modules\Content\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use DateTimeImmutable;

final class PostEntity
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

    public ?string $caption = null {
        get => $this->caption;
        set => $this->caption = $value;
    }

    public ?string $text = null {
        get => $this->text;
        set => $this->text = $value;
    }

    public ?string $fragment = null {
        get => $this->fragment;
        set => $this->fragment = $value;
    }

    public string $template {
        get => $this->template;
        set => $this->template = $value;
    }

    public bool $published = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public ?DateTimeImmutable $publishedAt = null {
        get => $this->publishedAt;
        set => $this->publishedAt = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public ?Meta $meta = null {
        get => $this->meta;
        set => $this->meta = $value;
    }

    public ?int $categoryId = null {
        get => $this->categoryId;
        set => $this->categoryId = $value;
    }

    public bool $oldRender = false {
        get => $this->oldRender;
        set => $this->oldRender = $value;
    }

    public function __construct(
        string $name,
        Slug $slug,
        string $template,
        ?int $categoryId = null,
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->template = $template;
        $this->categoryId = $categoryId;
    }

    public function publish(?DateTimeImmutable $date = null): void
    {
        $this->published = true;
        $this->publishedAt = $date ?? new DateTimeImmutable();
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
