<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\ParserLog;

use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogItemData;
use App\Modules\Parser\Application\DTOs\ParserLog\ParserLogShowData;
use App\Modules\Parser\Application\Interfaces\ParserLogRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ViewParserLogUseCase
{
    public function __construct(
        private ParserLogRepositoryInterface $logRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): ParserLogShowData
    {
        if (!$userPermission->can('parser.product.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        $log = $this->logRepository->getById($id);

        $newItems = array_map(
            fn($item) => ParserLogItemData::fromEntity($item),
            $this->logRepository->getLogItemsByStatus($id, 'new')
        );

        $changeItems = array_map(
            fn($item) => ParserLogItemData::fromEntity($item),
            $this->logRepository->getLogItemsByStatus($id, 'price_changed')
        );

        $delItems = array_map(
            fn($item) => ParserLogItemData::fromEntity($item),
            $this->logRepository->getLogItemsByStatus($id, 'deleted')
        );

        return ParserLogShowData::fromEntity($log, $newItems, $changeItems, $delItems);
    }
}
