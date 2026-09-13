<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\PageViewEntity;
use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\PageView;
use Carbon\CarbonInterface;
use DateTimeImmutable;

class PageViewRepository implements PageViewRepositoryInterface
{
    public function findById(int $id): ?PageViewEntity
    {
        $model = PageView::find($id);

        return $model ? $this->hydrate($model) : null;
    }

    public function create(PageViewEntity $view): PageViewEntity
    {
        $model = new PageView();
        $this->fill($model, $view);
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function findLastByVisitorInSession(int $visitorId, int $sessionId): ?PageViewEntity
    {
        $model = PageView::where('visitor_id', $visitorId)
            ->where('session_id', $sessionId)
            ->orderByDesc('viewed_at')
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function findLastByVisitor(int $visitorId): ?PageViewEntity
    {
        $model = PageView::where('visitor_id', $visitorId)
            ->orderByDesc('viewed_at')
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function finalizeView(int $pageViewId, int $duration, ?int $scrollDepth, bool $isExit): void
    {
        PageView::whereKey($pageViewId)->update([
            'duration' => $duration,
            'scroll_depth' => $scrollDepth,
            'is_exit' => $isExit,
        ]);
    }

    public function markAsExit(int $pageViewId, DateTimeImmutable $at): void
    {
        PageView::whereKey($pageViewId)->update(['is_exit' => true]);
    }

    public function markAsBounce(int $pageViewId): void
    {
        PageView::whereKey($pageViewId)->update(['is_bounce' => true]);
    }

    public function findBySession(int $sessionId): array
    {
        return PageView::where('session_id', $sessionId)
            ->orderBy('viewed_at')
            ->get()
            ->map(fn(PageView $model) => $this->hydrate($model))
            ->all();
    }

    public function countBySession(int $sessionId): int
    {
        return PageView::where('session_id', $sessionId)->count();
    }

    public function findByVisitor(int $visitorId, int $limit = 100, int $offset = 0): array
    {
        return PageView::where('visitor_id', $visitorId)
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(PageView $model) => $this->hydrate($model))
            ->all();
    }

    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, ?string $pageType = null, int $limit = 5000): array
    {
        $query = PageView::whereBetween('viewed_at', [$from, $to]);
        if ($pageType !== null) {
            $query->where('page_type', $pageType);
        }

        return $query->orderBy('viewed_at')
            ->limit($limit)
            ->get()
            ->map(fn(PageView $model) => $this->hydrate($model))
            ->all();
    }

    public function findTopEntities(DateTimeImmutable $from, DateTimeImmutable $to, string $pageType, int $limit = 20): array
    {
        return PageView::whereBetween('viewed_at', [$from, $to])
            ->where('page_type', $pageType)
            ->whereNotNull('entity_id')
            ->select('entity_id')
            ->selectRaw('COUNT(*) as views')
            ->groupBy('entity_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn($row) => ['entity_id' => (int)$row->entity_id, 'views' => (int)$row->views])
            ->all();
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return PageView::where('viewed_at', '<', $before)->delete();
    }

    private function fill(PageView $model, PageViewEntity $view): void
    {
        $model->visitor_id = $view->visitorId;
        $model->session_id = $view->sessionId;
        $model->page_type = $view->pageType;
        $model->entity_id = $view->entityId;
        $model->url = $view->url;
        $model->path = $view->path;
        $model->title = $view->title;
        $model->referrer = $view->referrer;
        $model->viewed_at = $view->viewedAt;
        $model->duration = $view->duration;
        $model->scroll_depth = $view->scrollDepth;
        $model->is_entry = $view->isEntry;
        $model->is_exit = $view->isExit;
        $model->is_bounce = $view->isBounce;
        $model->is_bot = $view->isBot;
    }

    private function hydrate(PageView $model): PageViewEntity
    {
        $view = new PageViewEntity(
            visitorId: (int)$model->visitor_id,
            sessionId: (int)$model->session_id,
            pageType: $model->page_type,
            url: $model->url,
            path: $model->path,
            viewedAt: DateTimeImmutable::createFromInterface($model->viewed_at),
        );

        $view->id = $model->id;
        $view->entityId = $model->entity_id;
        $view->title = $model->title;
        $view->referrer = $model->referrer;
        $view->duration = $model->duration;
        $view->scrollDepth = $model->scroll_depth;
        $view->isEntry = (bool)$model->is_entry;
        $view->isExit = (bool)$model->is_exit;
        $view->isBounce = (bool)$model->is_bounce;
        $view->isBot = (bool)$model->is_bot;
        $view->createdAt = $this->immutable($model->created_at);

        return $view;
    }

    private function immutable(?CarbonInterface $value): ?DateTimeImmutable
    {
        return $value === null ? null : DateTimeImmutable::createFromInterface($value);
    }
}
