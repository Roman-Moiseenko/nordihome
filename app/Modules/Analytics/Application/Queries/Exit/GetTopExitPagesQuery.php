<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Exit;

use App\Modules\Analytics\Domain\Interfaces\ExitRepositoryInterface;
use DateTimeImmutable;

/**
 * GetTopExitPages — топ страниц выхода за период.
 */
final class GetTopExitPagesQuery
{
    public function __construct(
        private readonly ExitRepositoryInterface $exits,
    ) {}

    /**
     * @return array<int, array{url: string, page_type: string, entity_id: int|null, exits: int}>
     */
    public function execute(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array
    {
        return $this->exits->findTopExitPages($from, $to, $limit);
    }
}
