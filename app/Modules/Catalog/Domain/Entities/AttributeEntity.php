<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use DomainException;

final class AttributeEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public AttributeType $type {
        get => $this->type;
        set => $this->type = $value;
    }

    public ?int $groupId = null {
        get => $this->groupId;
        set => $this->groupId = $value;
    }

    public bool $multiple = false {
        get => $this->multiple;
        set => $this->multiple = $value;
    }

    public bool $filter = true {
        get => $this->filter;
        set => $this->filter = $value;
    }

    public bool $showIn = true {
        get => $this->showIn;
        set => $this->showIn = $value;
    }

    public ?string $sameAs = null {
        get => $this->sameAs;
        set => $this->sameAs = $value;
    }

    /** @var AttributeVariantEntity[] */
    public array $variants = [] {
        get => $this->variants;
    }

    public function __construct(
        string $name,
        AttributeType $type,
        ?int $groupId = null,
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->groupId = $groupId;
    }

    public function addVariant(string $name): AttributeVariantEntity
    {
        $variant = new AttributeVariantEntity($name);
        $this->variants[] = $variant;

        return $variant;
    }

    public function removeVariant(int $id): void
    {
        foreach ($this->variants as $key => $variant) {
            if ($variant->id === $id) {
                unset($this->variants[$key]);
                $this->variants = array_values($this->variants);

                return;
            }
        }
    }

    public function renameVariant(int $id, string $name): void
    {
        $variant = $this->findVariant($id);
        $variant->rename($name);
    }

    public function findVariant(int $id): AttributeVariantEntity
    {
        foreach ($this->variants as $variant) {
            if ($variant->id === $id) {
                return $variant;
            }
        }

        throw new DomainException('Не найден вариант id = ' . $id . ' атрибута ' . $this->name);
    }

    public function isVariant(): bool
    {
        return $this->type->isVariant();
    }
}
