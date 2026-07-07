<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\DTOs\ParserLog;

use App\Modules\Parser\Domain\Entities\ParserLogEntity;
use Spatie\LaravelData\Data;

class ParserLogIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly bool $isRead,
        public readonly int $new,
        public readonly int $change,
        public readonly int $del,
    ) {}

    public static function fromEntity(ParserLogEntity $log, array $statusCounts): self
    {
        return new self(
            id: $log->id,
            date: $log->date,
            isRead: $log->isRead(),
            new: $statusCounts['new'] ?? 0,
            change: $statusCounts['price_changed'] ?? 0,
            del: $statusCounts['deleted'] ?? 0,
        );
    }
}
