<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Domain\Entities;

final class FormBackEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $url {
        get => $this->url;
        set => $this->url = $value;
    }

    public string $formName {
        get => $this->formName;
        set => $this->formName = $value;
    }

    public array $data {
        get => $this->data;
        set => $this->data = $value;
    }

    public ?\DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public function __construct(
        string $url,
        string $formName,
        array $data,
    ) {
        $this->url = $url;
        $this->formName = $formName;
        $this->data = $data;
    }
}
