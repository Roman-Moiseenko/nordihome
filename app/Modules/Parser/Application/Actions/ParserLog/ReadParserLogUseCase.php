<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\ParserLog;

use App\Modules\Parser\Application\Interfaces\ParserLogRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ReadParserLogUseCase
{
    public function __construct(
        private ParserLogRepositoryInterface $logRepository,
    ) {}

    public function execute(int $id, int $staffId, UserPermission $userPermission): void
    {
        if (!$userPermission->can('parser.product.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        $log = $this->logRepository->getById($id);

        $log->staffId = $staffId;
        $log->readAt = new \DateTimeImmutable();

        $this->logRepository->saveLog($log);
    }
}
