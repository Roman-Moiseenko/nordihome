<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\ParserLog;

use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogIndexData;
use App\Modules\Parser\Application\Interfaces\ParserLogRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use DomainException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexParserLogUseCase
{
    public function __construct(
        private ParserLogRepositoryInterface $logRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<ParserLogIndexData>
     */
    public function execute(UserPermission $userPermission, int $perPage = 20): LengthAwarePaginator
    {
        if (!$userPermission->can('parser.product.view')) {
            throw new DomainException('Доступ запрещён');
        }

        $paginator = $this->logRepository->getLogsPaginated($perPage);

        return $paginator->through(function (mixed $log) {
            if (!$log instanceof \App\Modules\Parser\Domain\Entities\ParserLogEntity) {
                return $log;
            }

            $statusCounts = $this->logRepository->getLogItemsCountsByStatus($log->id);

            return ParserLogIndexData::fromEntity($log, $statusCounts);
        });
    }
}
