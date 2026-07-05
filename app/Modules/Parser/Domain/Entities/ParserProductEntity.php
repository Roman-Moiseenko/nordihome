<?php

namespace App\Modules\Parser\Domain\Entities;

use App\Modules\Parser\Domain\ValueObjects\Composite;
use App\Modules\Parser\Domain\ValueObjects\Package;
use App\Modules\Shared\Domain\ValueObjects\Slug;



final class ParserProductEntity
{
    // ======================== Идентификатор ========================

    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $productId = null {
        get => $this->productId;
        set => $this->productId = $value;
    }

    // ======================== Основные поля ========================
    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }
    public string $code {
        get => $this->code;
        set => $this->code = $value;
    }

    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    /** @var Composite[] $composite */
    public array $composite = [] {
        get => $this->composite;
        set => $this->composite = $value;
    }

    public string $url = '' {
        get => $this->name;
        set => $this->name = $value;
    }
    public float $priceSell = 0 {
        get => $this->priceSell;
        set => $this->priceSell = $value;
    }
    public float $priceBase = 0 {
        get => $this->priceBase;
        set => $this->priceBase = $value;
    }

    public string $short = '' {
        get => $this->short;
        set => $this->short = $value;
    }

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    /** @var Package[] $packages */
    public array $packages = [] {
        get => $this->packages;
        set => $this->packages = $value;
    }

    public bool $fragile = false {
        get => $this->fragile;
        set => $this->fragile = $value;
    }

    public bool $sanctioned = false {
        get => $this->sanctioned;
        set => $this->sanctioned = $value;
    }

    public bool $availability = false {
        get => $this->availability;
        set => $this->availability = $value;
    }
    public ?\DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }
    public array $colors = [] {
        get => $this->colors;
        set => $this->colors = $value;
    }

    public ?\DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }
    public function __construct(
        string $name,
        string $code,
    )
    {
        $this->name = $name;
        $this->code = $code;
        $this->slug = new Slug($name);
    }
}
