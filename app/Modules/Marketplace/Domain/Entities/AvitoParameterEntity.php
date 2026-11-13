<?php

namespace App\Modules\Marketplace\Domain\Entities;

final class AvitoParameterEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public ?string $value = null {
        get => $this->value;
        set => $this->value = $value;
    }

    /** @var string[] */
    public array $variants = [] {
        get => $this->variants;
        set => $this->variants = $value;
    }

    public bool $isHtml = false {
        get => $this->isHtml;
        set => $this->isHtml = $value;
    }

    public function __construct(
        string $name,
        string $slug,
        ?string $value = null,
        array $variants = [],
        bool $isHtml = false,
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->value = $value;
        $this->variants = $variants;
        // Если есть варианты — это селект, а не html-редактор.
        $this->isHtml = !empty($variants) ? false : $isHtml;
    }
}
