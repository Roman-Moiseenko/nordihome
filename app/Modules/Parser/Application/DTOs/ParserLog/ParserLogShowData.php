<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\DTOs\ParserLog;

use App\Modules\Parser\Domain\Entities\ParserLogEntity;
use Spatie\LaravelData\Data;

class ParserLogShowData extends Data
{
    /**
     * @param ParserLogItemData[] $new
     * @param ParserLogItemData[] $change
     * @param ParserLogItemData[] $del
     */
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly bool $read,
        public readonly array $new,
        public readonly array $change,
        public readonly array $del,
    ) {}

    /**
     * @param ParserLogItemData[] $newItems
     * @param ParserLogItemData[] $changeItems
     * @param ParserLogItemData[] $delItems
     */
    public static function fromEntity(
        ParserLogEntity $log,
        array $newItems,
        array $changeItems,
        array $delItems,
    ): self {
        return new self(
            id: $log->id,
            date: $log->date,
            read: $log->isRead(),
            new: $newItems,
            change: $changeItems,
            del: $delItems,
        );
    }
}
