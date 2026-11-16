<?php

namespace App\Modules\Marketplace\Domain\Entities;

final class AvitoProductValueEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $avitoProductId {
        get => $this->avitoProductId;
        set => $this->avitoProductId = $value;
    }

    public int $avitoCategoryFieldId {
        get => $this->avitoCategoryFieldId;
        set => $this->avitoCategoryFieldId = $value;
    }

    public ?string $value = null {
        get => $this->value;
        set => $this->value = $value;
    }

    public function __construct(
        int $avitoProductId,
        int $avitoCategoryFieldId,
        ?string $value = null,
    ) {
        $this->avitoProductId = $avitoProductId;
        $this->avitoCategoryFieldId = $avitoCategoryFieldId;
        $this->value = $value;
    }
}
