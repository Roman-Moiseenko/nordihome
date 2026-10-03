<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

use DateTimeImmutable;

final class PricingRuleEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public float $ratioWeight {
        get => $this->ratioWeight;
        set => $this->ratioWeight = $value;
    }

    public float $ratioMarkup {
        get => $this->ratioMarkup;
        set => $this->ratioMarkup = $value;
    }

    public int $roundingStep {
        get => $this->roundingStep;
        set => $this->roundingStep = $value;
    }

    public int $roundingSubtract {
        get => $this->roundingSubtract;
        set => $this->roundingSubtract = $value;
    }

    public bool $isActive = true {
        get => $this->isActive;
        set => $this->isActive = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    /**
     * Количество привязанных категорий (заполняется репозиторием для списка).
     */
    public int $categoriesCount = 0 {
        get => $this->categoriesCount;
        set => $this->categoriesCount = $value;
    }

    public function __construct(
        string $name,
        float $ratioWeight,
        float $ratioMarkup,
        int $roundingStep = 100,
        int $roundingSubtract = 10,
        bool $isActive = true,
    ) {
        $this->name = $name;
        $this->ratioWeight = $ratioWeight;
        $this->ratioMarkup = $ratioMarkup;
        $this->roundingStep = $roundingStep;
        $this->roundingSubtract = $roundingSubtract;
        $this->isActive = $isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
