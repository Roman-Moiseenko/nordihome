<?php

namespace App\Modules\Accounting\Application\DTOs\Exchange;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
class StockPayloadData extends Data
{
    /**
     * @param string $event
     * @param DataCollection $items
     */
    public function __construct(
        #[Required, In('1c.stock')]
        public readonly string $event,

        #[DataCollectionOf(StockItemData::class)]
        #[Min(1), Max(100)]
        public readonly DataCollection $items,
    ) {}


}
