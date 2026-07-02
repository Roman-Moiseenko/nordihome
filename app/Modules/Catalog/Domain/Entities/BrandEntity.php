<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

final class BrandEntity
{
    public const int DEFAULT_ID = 1;
    public const string IKEA = 'Икеа';
    public const string NB = 'New Balance';

    // ======================== Основные поля ========================
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    public string $url = '' {
        get => $this->url;
        set => $this->url = $value;
    }

    /** @var array<string> */
    public array $sameAs = [] {
        get => $this->sameAs;
        set => $this->sameAs = $value;
    }

    public ?string $image_url = null {
        get => $this->image_url;
        set => $this->image_url = $value;
    }

    public ?int $currencyId = null {
        get => $this->currencyId;
        set => $this->currencyId = $value;
    }

    public ?string $parserClass = null {
        get => $this->parserClass;
        set => $this->parserClass = $value;
    }

    public function __construct(
        string $name,
        string $description = '',
        string $url = '',
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
    }
}
