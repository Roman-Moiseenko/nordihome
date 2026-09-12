<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Jobs;

use App\Modules\Analytics\Application\Actions\Visitor\IdentifyVisitorUseCase;
use App\Modules\Analytics\Application\Actions\Visitor\LinkVisitorToClientUseCase;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * LinkVisitorToClientJob — привязка посетителя к client_id после логина.
 *
 * Если у клиента уже есть visitor с другим uuid — канонизирует (переносит uuid),
 * сохраняя историю. Если посетителя ещё нет — создаёт его с привязкой.
 */
final class LinkVisitorToClientJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $uuid,
        public readonly int $clientId,
    ) {}

    public function handle(
        VisitorRepositoryInterface $visitors,
        LinkVisitorToClientUseCase $linkVisitor,
        IdentifyVisitorUseCase $identifyVisitor,
    ): void {
        $uuid = new VisitorUuid($this->uuid);

        $visitor = $visitors->findByUuid($uuid);

        if ($visitor !== null && $visitor->id !== null) {
            $linkVisitor->execute($visitor->id, $this->clientId);

            return;
        }

        $existing = $visitors->findByClientId($this->clientId);

        if ($existing !== null && $existing->id !== null) {
            // Канонизация: переносим историю под новый uuid из cookie.
            $visitors->changeUuid($existing->id, $uuid);
            $linkVisitor->execute($existing->id, $this->clientId);

            return;
        }

        $identifyVisitor->execute((string) $uuid, clientId: $this->clientId);
    }
}
