<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Visitor;

use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use DateTimeImmutable;
use DomainException;

/**
 * LinkVisitorToClient — привязка анонимного посетителя к клиенту после логина.
 *
 * Проставляет client_id и client_linked_at, сохраняя всю анонимную историю.
 */
final readonly class LinkVisitorToClientUseCase
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
    ) {}

    public function execute(int $visitorId, int $clientId): VisitorEntity
    {
        $visitor = $this->visitors->findById($visitorId);

        if ($visitor === null) {
            throw new DomainException("Посетитель #{$visitorId} не найден.");
        }

        if ($visitor->clientId === $clientId) {
            return $visitor;
        }

        $now = new DateTimeImmutable();
        $visitor->linkClient($clientId, $now);

        $this->visitors->linkToClient($visitorId, $clientId, $now);

        return $visitor;
    }
}
