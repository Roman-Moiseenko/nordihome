<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Path;

use App\Modules\Analytics\Domain\Entities\PathStepEntity;
use App\Modules\Analytics\Domain\Interfaces\PathRepositoryInterface;
use DateTimeImmutable;

/**
 * RecordPathStep — запись шага пути клиента в рамках сессии.
 *
 * Порядковый номер шага вычисляется как количество уже записанных шагов
 * сессии + 1.
 */
final readonly class RecordPathStepUseCase
{
    public function __construct(
        private PathRepositoryInterface $paths,
    ) {}

    public function execute(
        int $sessionId,
        int $visitorId,
        string $pageType,
        string $url,
        ?int $entityId = null,
        ?int $duration = null,
        ?string $actionType = null,
        ?DateTimeImmutable $occurredAt = null,
    ): void {
        $stepNumber = count($this->paths->findBySession($sessionId)) + 1;

        $step = new PathStepEntity(
            $sessionId,
            $visitorId,
            $stepNumber,
            $pageType,
            $url,
            $occurredAt ?? new DateTimeImmutable(),
        );

        $step->entityId = $entityId;
        $step->duration = $duration;
        $step->actionType = $actionType;

        $this->paths->create($step);
    }
}
