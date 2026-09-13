<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Support;

use App\Modules\Analytics\Application\Actions\Action\TrackActionUseCase;
use App\Modules\Analytics\Application\DTOs\Action\TrackActionData;
use App\Modules\Analytics\Infrastructure\Services\VisitorUuidGenerator;

/**
 * RecordsAnalyticsAction — запись действия клиента (analytics_actions) из
 * presentation/инфраструктурного слоя: HTTP-контроллеров и Livewire-компонентов.
 *
 * Правило модуля: TrackActionUseCase НЕ вызывается из других UseCase —
 * только отсюда (или напрямую из контроллера/метода Livewire).
 */
trait RecordsAnalyticsAction
{
    protected function recordAnalyticsAction(
        string $actionType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $payload = null,
    ): void {
        app(TrackActionUseCase::class)->execute(
            TrackActionData::from([
                'actionType' => $actionType,
                'entityType' => $entityType,
                'entityId' => $entityId,
                'payload' => $payload,
            ]),
            request()->cookie(VisitorUuidGenerator::COOKIE_NAME),
        );
    }
}
