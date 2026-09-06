<?php

namespace App\Modules\Guide\Domain\Entities;

use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class AdditionEntity
{

    public ?int  $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?string $name = null {
        get => $this->name;
        set => $this->name = $value;
    }

    public int  $base = 0 {
        get => $this->base;
        set => $this->base = $value;
    }

    public AdditionType $type {
        get => $this->type;
        set => $this->type = $value;
    }

    public bool  $manual = false {
        get => $this->manual;
        set => $this->manual = $value;
    }

    public bool  $isQuantity = false {
        get => $this->isQuantity;
        set => $this->isQuantity = $value;
    }

    public ?string $class = null {
        get => $this->class;
        set => $this->class = $value;
    }
    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public function __construct(
        string $name,
        Slug $slug,
        AdditionType $type
    )
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->type = $type;
    }

}
