<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Search;

use App\Modules\Analytics\Domain\Interfaces\SearchRepositoryInterface;

/**
 * RegisterSearchClick — фиксация клика по результату поисковой выдачи.
 *
 * Дополняет запись поиска данными о том, по какому результату (id, тип)
 * и на какой позиции кликнул посетитель.
 */
final class RegisterSearchClickUseCase
{
    public function __construct(
        private readonly SearchRepositoryInterface $searches,
    ) {}

    public function execute(
        int $searchId,
        int $resultId,
        string $resultType,
        int $position,
    ): void {
        $this->searches->registerClick($searchId, $resultId, $resultType, $position);
    }
}
