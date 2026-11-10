<?php

namespace App\Modules\Marketplace\Domain\Entities;

final class AvitoCategoryFieldEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $avitoCategoryId {
        get => $this->avitoCategoryId;
        set => $this->avitoCategoryId = $value;
    }

    public string $tag {
        get => $this->tag;
        set => $this->tag = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public string $type {
        get => $this->type;
        set => $this->type = $value;
    }

    public bool $isRequired = false {
        get => $this->isRequired;
        set => $this->isRequired = $value;
    }

    public ?array $allowedValues = null {
        get => $this->allowedValues;
        set => $this->allowedValues = $value;
    }

    public ?array $dependsOn = null {
        get => $this->dependsOn;
        set => $this->dependsOn = $value;
    }

    public function __construct(
        int $avitoCategoryId,
        string $tag,
        string $name,
        string $type,
        bool $isRequired = false,
        ?array $allowedValues = null,
        ?array $dependsOn = null,
    ) {
        $this->avitoCategoryId = $avitoCategoryId;
        $this->tag = $tag;
        $this->name = $name;
        $this->type = $type;
        $this->isRequired = $isRequired;
        $this->allowedValues = $allowedValues;
        $this->dependsOn = $dependsOn;
    }
}
