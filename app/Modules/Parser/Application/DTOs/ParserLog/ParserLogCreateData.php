<?php

namespace App\Modules\Parser\Application\DTOs\ParserLog;

use App\Modules\Parser\Domain\ValueObjects\ParserStatus;
use App\Modules\Parser\Domain\ValueObjects\PriceChangePayload;
use Spatie\LaravelData\Data;

class ParserLogCreateData extends Data
{
    public function __construct(
        public readonly ParserStatus $status,
        public readonly ?int $parserId = null,
        public readonly ?PriceChangePayload $payload = null,
        public readonly ?string $error = null,
    )
    {
    }
}
