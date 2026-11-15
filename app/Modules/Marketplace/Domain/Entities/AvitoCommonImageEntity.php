<?php

namespace App\Modules\Marketplace\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Image;

/**
 * Общее изображение Авито.
 *
 * Каждая запись — одно изображение с названием, признаком активности и
 * глобальным порядком сортировки. Само изображение хранится в полиморфной
 * таблице photos с model_type "marketplace.avito-common-image" и type "image".
 */
final class AvitoCommonImageEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?string $name = null {
        get => $this->name;
        set => $this->name = $value;
    }

    public bool $active = true {
        get => $this->active;
        set => $this->active = $value;
    }

    public int $sort = 0 {
        get => $this->sort;
        set => $this->sort = $value;
    }

    public ?Image $image = null {
        get => $this->image;
        set => $this->image = $value;
    }

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
