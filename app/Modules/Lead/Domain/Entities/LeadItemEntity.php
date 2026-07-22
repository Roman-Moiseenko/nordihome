<?php

namespace App\Modules\Lead\Domain\Entities;

use DateTimeImmutable;

final class LeadItemEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $staffId {
        get => $this->staffId;
        set => $this->staffId = $value;
    }

    public ?int $type = null {
        get => $this->type;
        set => $this->type = $value;
    }

    public string $comment {
        get => $this->comment;
        set => $this->comment = $value;
    }

    public DateTimeImmutable $createdAt {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $finishedAt = null {
        get => $this->finishedAt;
        set => $this->finishedAt = $value;
    }

    public function __construct(
        string $comment,
        int $staffId,
    ) {
        $this->comment = $comment;
        $this->staffId = $staffId;
    }
}
