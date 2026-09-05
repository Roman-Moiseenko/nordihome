<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

class LeadInfoData
{
    public function __construct(
        public int $id,
        public ?string $finishedAt,
        public ?string $createdAt,
        public string $status,
    )
    {
    }
}
