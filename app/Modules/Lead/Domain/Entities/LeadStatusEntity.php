<?php

namespace App\Modules\Lead\Domain\Entities;

use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use DateTimeImmutable;

final class LeadStatusEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public LeadStatusValue $value {
        get => $this->value;
        set => $this->value = $value;
    }

    public DateTimeImmutable $createdAt {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public function __construct(
        LeadStatusValue $value,
    ) {
        $this->value = $value;
        $this->createdAt = new \DateTimeImmutable();
    }
}
