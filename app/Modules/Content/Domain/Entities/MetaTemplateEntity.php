<?php

namespace App\Modules\Content\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Meta;

final class MetaTemplateEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $class {
        get => $this->class;
        set => $this->class = $value;
    }

    public ?string $entity = null {
        get => $this->entity;
        set => $this->entity = $value;
    }

    public string $templateTitle = '' {
        get => $this->templateTitle;
        set => $this->templateTitle = $value;
    }

    public string $templateDescription = '' {
        get => $this->templateDescription;
        set => $this->templateDescription = $value;
    }

    public function __construct(
        string $class,
        ?string $entity = null,
    ) {
        $this->class = $class;
        $this->entity = $entity;
    }
}
