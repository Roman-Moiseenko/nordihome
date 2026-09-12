<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Path;

use App\Modules\Analytics\Domain\Entities\PathStepEntity;
use App\Modules\Analytics\Domain\Interfaces\PathRepositoryInterface;

/**
 * GetSessionPath — путь клиента по сессии (шаги в хронологическом порядке).
 */
final class GetSessionPathQuery
{
    public function __construct(
        private readonly PathRepositoryInterface $paths,
    ) {}

    /**
     * @return PathStepEntity[]
     */
    public function execute(int $sessionId): array
    {
        return $this->paths->findBySession($sessionId);
    }
}
