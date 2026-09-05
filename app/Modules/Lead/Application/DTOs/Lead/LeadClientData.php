<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use App\Modules\Auth\Domain\Entities\ClientEntity;
use Spatie\LaravelData\Data;

class LeadClientData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $fullName,
    )
    {
    }

    public static function fromEntity(ClientEntity $client): self
    {
        return new self(
            id: $client->id,
            fullName: $client->fullName->getValue(),
        );
    }
}
