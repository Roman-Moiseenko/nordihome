<?php

namespace App\Modules\Parser\Domain\Entities;

use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Domain\ValueObjects\PriceChangePayload;

class ParserLogItemEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $logId {
        get => $this->logId;
        set => $this->logId = $value;
    }

    public ?int $parserId = null {
        get => $this->parserId;
        set => $this->parserId = $value;
    }

    public ParserStatus $status {
        get => $this->status;
        set => $this->status = $value;
    }

    public ?PriceChangePayload $payload = null {
        get => $this->payload;
        set => $this->payload = $value;
    }
    public ?string $error = null {
        get => $this->error;
        set => $this->error = $value;
    }

    public function __construct(
        int                 $logId,
        ParserStatus        $status,
        ?int                $parserId = null,
        ?PriceChangePayload $payload = null,
        ?int                $id = null,
        ?string             $error = null,
    )
    {
        $this->logId = $logId;
        $this->parserId = $parserId;
        $this->status = $status;
        $this->payload = $payload;
        $this->id = $id;
        $this->error = $error;
    }
}
