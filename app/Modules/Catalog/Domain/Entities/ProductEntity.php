<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;

final class ProductEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public Code $code {
        get => $this->code;
        set => $this->code = $value;
    }

    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public string $namePrint {
        get => $this->namePrint;
        set => $this->namePrint = $value;
    }

    public string $oldSlug = '' {
        get => $this->oldSlug;
        set => $this->oldSlug = $value;
    }

    public int $brandId {
        get => $this->brandId;
        set => $this->brandId = $value;
    }

    public ?int $seriesId = null {
        get => $this->seriesId;
        set => $this->seriesId = $value;
    }

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    public string $short = '' {
        get => $this->short;
        set => $this->short = $value;
    }

    public string $comment = '' {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public string $model = '' {
        get => $this->model;
        set => $this->model = $value;
    }

    public string $barcode = '' {
        get => $this->barcode;
        set => $this->barcode = $value;
    }

    public int $mainCategoryId {
        get => $this->mainCategoryId;
        set => $this->mainCategoryId = $value;
    }

    public int $frequency = 105 {
        get => $this->frequency;
        set => $this->frequency = $value;
    }

    // ======================== Справочники ========================
    public ?int $vatId = null {
        get => $this->vatId;
        set => $this->vatId = $value;
    }

    public ?int $countryId = null {
        get => $this->countryId;
        set => $this->countryId = $value;
    }

    public ?int $measuringId = null {
        get => $this->measuringId;
        set => $this->measuringId = $value;
    }

    public ?int $markingTypeId = null {
        get => $this->markingTypeId;
        set => $this->markingTypeId = $value;
    }

    // ======================== Булевы флаги ========================
    public bool $published = false {
        get => $this->published;
    }

    public ?\DateTimeImmutable $publishedAt = null {
        get => $this->publishedAt;
    }

    public bool $preOrder = true {
        get => $this->preOrder;
        set => $this->preOrder = $value;
    }

    public bool $delivery = false {
        get => $this->delivery;
        set => $this->delivery = $value;
    }

    public bool $local = false {
        get => $this->local;
        set => $this->local = $value;
    }

    public bool $priority = false {
        get => $this->priority;
        set => $this->priority = $value;
    }

    public bool $notSale = false {
        get => $this->notSale;
        set => $this->notSale = $value;
    }

    public bool $priceReduced = false {
        get => $this->priceReduced;
        set => $this->priceReduced = $value;
    }

    public bool $onlyOnOrder = false {
        get => $this->onlyOnOrder;
        set => $this->onlyOnOrder = $value;
    }

    public bool $fractional = false {
        get => $this->fractional;
        set => $this->fractional = $value;
    }

    public bool $hidePrice = false {
        get => $this->hidePrice;
        set => $this->hidePrice = $value;
    }

    public function __construct(
        string $name,
        Code $code,
        Slug $slug,
        int $mainCategoryId,
        int $brandId,
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->slug = $slug;
        $this->mainCategoryId = $mainCategoryId;
        $this->brandId = $brandId;
        $this->namePrint = $name;
        $this->published = false;
    }

    public function publish(): void
    {
        if (!$this->published) {
            $this->published = true;
            $this->publishedAt = new \DateTimeImmutable();
        }
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
