<?php

namespace App\Modules\Order\Domain\Entities;

class OrderLoggerEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public ?int $orderId = null {
        get => $this->orderId;
        set => $this->orderId = $value;
    }
    public ?int $staffId = null {
        get => $this->staffId;
        set => $this->staffId = $value;
    }

    public ?\DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }
    public string $action {
        get => $this->action;
        set => $this->action = $value;
    }

    public ?string $object = null {
        get => $this->object;
        set => $this->object = $value;
    }
    public ?string $old = null {
        get => $this->old;
        set => $this->old = $value;
    }
    public ?string $value = null {
        get => $this->value;
        set => $this->value = $value;
    }

    public ?string $link = null {
        get => $this->link;
        set => $this->link = $value;
    }

    public function __construct(
        int $orderId,
        ?int $staffId,
        string $action,
    )
    {
        $this->orderId = $orderId;
        $this->staffId = $staffId;
        $this->action = $action;
    }
}
