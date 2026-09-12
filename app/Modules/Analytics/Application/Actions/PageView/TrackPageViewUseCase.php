<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\PageView;

use App\Modules\Analytics\Domain\Entities\PageViewEntity;
use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;
use DateTimeImmutable;

/**
 * TrackPageView — регистрация просмотра страницы.
 *
 * Если это первый просмотр в сессии — помечает его как вход (entry).
 * Обновляет счётчик page_views_count и last_activity_at сессии,
 * кладёт page_view_id в VisitorContext.
 */
final readonly class TrackPageViewUseCase
{
    public function __construct(
        private PageViewRepositoryInterface $pageViews,
        private SessionRepositoryInterface  $sessions,
        private VisitorContextInterface     $context,
    ) {}

    public function execute(
        int $visitorId,
        int $sessionId,
        string $pageType,
        string $url,
        ?int $entityId = null,
        ?string $title = null,
        ?string $referrer = null,
    ): PageViewEntity {
        $now = new DateTimeImmutable();

        $path = parse_url($url, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';

        $last = $this->pageViews->findLastByVisitorInSession($visitorId, $sessionId);

        $view = new PageViewEntity($visitorId, $sessionId, $pageType, $url, $path, $now);
        $view->entityId = $entityId;
        $view->title = $title;
        $view->referrer = $referrer;

        if ($last === null) {
            $view->markAsEntry();
        }

        $view = $this->pageViews->create($view);

        $this->sessions->increment($sessionId, 'page_views_count');
        $this->sessions->touch($sessionId, $now);

        if ($view->id !== null) {
            $this->context->setPageViewId($view->id);
        }

        return $view;
    }
}
