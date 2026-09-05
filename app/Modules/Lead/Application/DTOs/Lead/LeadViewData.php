<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use Spatie\LaravelData\Data;

class LeadViewData extends Data
{
    public function __construct(
        public int $id,
        public ?int $staffId,
        public ?string $finishedAt,
        public ?string $createdAt,
        public string $name,
        public string $type,
        public string $status,
        public ?array $data,
        public ?LeadClientData $client = null,
        public ?LeadOrderData $order = null,
        /** @var LeadInfoData[] $leads */
        public ?array $leads = null,
        public string $comment,

        /** @var LeadCommentData[] $comments */
        public ?array $comments = null,
    )
    {

    }
}
