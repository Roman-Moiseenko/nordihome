<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use App\Modules\Auth\Application\DTOs\Client\ClientIndexData;
use App\Modules\Order\Application\DTOs\OrderViewData;
use Spatie\LaravelData\Data;

class LeadViewData extends Data
{
    public function __construct(
        public int $id,
        public ?string $finishedAt,
        public ?string $createdAt,
        public string $type,
        public string $status,
        public ?array $data,
        public ?ClientIndexData $client = null,
        public ?OrderViewData $order = null,
        /** @var LeadInfoData[] $leads */
        public ?array $leads = null,
    )
    {

    }
}
