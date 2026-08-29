<?php

declare(strict_types=1);

namespace App\Modules\Discount\Domain\Entities;

use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

/**
 * Акция (промо).
 *
 * Связанные товары (products) НЕ хранятся в сущности —
 * они получаются через отдельные UseCase.
 */
final class PromotionEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }


    public ?Meta $meta  = null {
        get => $this->meta;
        set => $this->meta = $value;
    }

    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public string $conditionUrl = '' {
        get => $this->conditionUrl;
        set => $this->conditionUrl = $value;
    }

    public bool $menu = false {
        get => $this->menu;
        set => $this->menu = $value;
    }

    public bool $showTitle = false {
        get => $this->showTitle;
        set => $this->showTitle = $value;
    }

    public int $discount = 0 {
        get => $this->discount;
        set => $this->discount = $value;
    }

    public bool $published = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public bool $active = false {
        get => $this->active;
        set => $this->active = $value;
    }

    public ?PromotionStatus $status = null {
        get => $this->status;
        set => $this->status = $value;
    }

    public ?\DateTimeImmutable $startAt = null {
        get => $this->startAt;
        set => $this->startAt = $value;
    }

    public ?\DateTimeImmutable $finishAt = null {
        get => $this->finishAt;
        set => $this->finishAt = $value;
    }

    public string $colorClass = 'red' {
        get => $this->colorClass;
        set => $this->colorClass = $value;
    }

    public string $positionClass = 'top-right' {
        get => $this->positionClass;
        set => $this->positionClass = $value;
    }

    public string $textTag = 'Акция' {
        get => $this->textTag;
        set => $this->textTag = $value;
    }

    public bool $showTag = true {
        get => $this->showTag;
        set => $this->showTag = $value;
    }

    public bool $showDiscount = true {
        get => $this->showDiscount;
        set => $this->showDiscount = $value;
    }

    public ?string $svg = null {
        get => $this->svg;
        set => $this->svg = $value;
    }

    public function __construct(
        string $name,
        Slug $slug,
    ) {
        $this->name = $name;
        $this->slug = $slug;
    }
}
