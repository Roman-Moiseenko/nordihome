<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Http\Middlewares;

use App\Modules\Analytics\Application\Actions\PageView\TrackPageViewUseCase;
use App\Modules\Analytics\Application\Actions\Path\RecordPathStepUseCase;
use App\Modules\Analytics\Application\Actions\Session\StartSessionUseCase;
use App\Modules\Analytics\Application\Actions\Visitor\IdentifyVisitorUseCase;
use App\Modules\Analytics\Infrastructure\Jobs\TrackPageViewJob;
use App\Modules\Analytics\Infrastructure\Services\PageTypeResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TrackPageViewMiddleware — фиксация просмотра страницы.
 *
 * Идентификация посетителя, старт сессии и создание page_view выполняются
 * синхронно ДО рендеринга ответа, чтобы id просмотра (page_view_id) попал в
 * VisitorContext и был доступен JS-трекеру на странице. Тяжёлые операции
 * (GeoIP и запись шага пути) выносятся в очередь analytics.
 *
 * Работает только для GET/HEAD. Боты пропускаются.
 */
final class TrackPageViewMiddleware
{
    public function __construct(
        private readonly PageTypeResolver $pageTypeResolver,
        private readonly IdentifyVisitorUseCase $identifyVisitor,
        private readonly StartSessionUseCase $startSession,
        private readonly TrackPageViewUseCase $trackPageView,
        private readonly RecordPathStepUseCase $recordPathStep,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $snapshot = $request->attributes->get('analytics_snapshot');

        if ($snapshot === null || !$this->isPageRequest($request) || $snapshot->isBot) {
            return $next($request);
        }

        [$pageType, $routeEntityId] = $this->pageTypeResolver->resolve($request);

        $entityId = $routeEntityId;
        if (!is_numeric($entityId)) {
            $entityId = $this->pageTypeResolver->resolveEntityIdBySlug($pageType, $request);
        }
        $entityId = is_numeric($entityId) ? (int) $entityId : null;

        // Синхронно: visitor -> session -> page_view.
        // Каждый use case кладёт свой id в VisitorContext (scoped на запрос),
        // поэтому к моменту рендеринга вида page_view_id уже известен.
        $visitor = $this->identifyVisitor->execute(
            (string) $snapshot->uuid,
            $snapshot->ip,
            $snapshot->userAgent,
            $snapshot->referrer,
            $snapshot->utm->toArray(),
            $snapshot->url,
            $snapshot->clientId,
            $snapshot->device->deviceType,
            $snapshot->device->os,
            $snapshot->device->browser,
        );

        $session = null;
        if ($visitor->id !== null) {
            $session = $this->startSession->execute(
                $visitor->id,
                $snapshot->url,
                $pageType,
                $entityId,
                $snapshot->referrer,
                $snapshot->utm->toArray(),
                $snapshot->ip,
                $snapshot->userAgent,
                $snapshot->device->deviceType,
                $snapshot->device->os,
                $snapshot->device->browser,
            );
        }

        if ($session !== null && $session->id !== null) {
            $this->trackPageView->execute(
                $visitor->id,
                $session->id,
                $pageType,
                $snapshot->url,
                $entityId,
                null,
                $snapshot->referrer,
            );

            $this->recordPathStep->execute(
                sessionId: $session->id,
                visitorId: $visitor->id,
                pageType: $pageType,
                url: $snapshot->url,
                entityId: $entityId,
                actionType: 'page_view',
            );
        }

        $response = $next($request);

        // Асинхронно: GeoIP (только при первом касании) + шаг пути.
        if ($visitor->id !== null && $session !== null && $session->id !== null) {
            $needGeo = $visitor->city === null && $visitor->country === null;

            dispatch(new TrackPageViewJob(
                snapshot: $snapshot,
                pageType: $pageType,
                entityId: $entityId,
                url: $snapshot->url,
                visitorId: $visitor->id,
                sessionId: $session->id,
                needGeo: $needGeo,
            ))->onQueue('analytics');
        }

        return $response;
    }

    private function isPageRequest(Request $request): bool
    {
        return $request->isMethod('GET') || $request->isMethod('HEAD');
    }
}
