<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

final class SeriesEntity
{

    // ======================== Основные поля ========================
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $nameRu = '' {
        get => $this->nameRu;
        set => $this->nameRu = $value;
    }


    public function __construct(
        string $name,
        string $nameRu = '',
    ) {
        $this->name = $name;
        $this->nameRu = $nameRu;
    }
}
