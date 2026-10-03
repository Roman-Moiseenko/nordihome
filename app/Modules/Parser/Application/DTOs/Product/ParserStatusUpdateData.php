<?php

namespace App\Modules\Parser\Application\DTOs\Product;

use App\Modules\Parser\Domain\ValueObjects\ParserStatus;

readonly class ParserStatusUpdateData
{
    public function __construct(
        public ?ParserStatus $status,
        public ?float        $previousPrice = null,
        public ?float        $newPrice = null,
        //public ?array        $store = null,
    )
    {

    }
}
