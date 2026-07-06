<?php

namespace App\Modules\Parser\Domain\Entities;

class ParserLogEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    /**
     * Формат YYYY-MM-DD, '2026-07-06'
     * @var string $date
     */
    public string $date {
        get => $this->date;
        set => $this->date = $value;
    }

    public ?\DateTimeImmutable $readAt = null {
        get => $this->readAt;
        set => $this->readAt = $value;
    }

    public ?int $staffId = null {
        get => $this->staffId;
        set => $this->staffId = $value;
    }


    public function __construct(
        string $date,
        ?\DateTimeImmutable $readAt = null,
        ?int $staffId = null,
        ?int $id = null,
    ) {
        $this->date = $date;
        $this->readAt = $readAt;
        $this->staffId = $staffId;
        $this->id = $id;
    }

    public function isRead(): bool
    {
        return !is_null($this->readAt);
    }
}
