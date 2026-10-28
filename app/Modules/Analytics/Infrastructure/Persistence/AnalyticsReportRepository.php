<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Interfaces\AnalyticsReportRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\Action;
use App\Modules\Analytics\Infrastructure\Models\PageView;
use App\Modules\Analytics\Infrastructure\Models\Path;
use App\Modules\Analytics\Infrastructure\Models\Search;
use App\Modules\Analytics\Infrastructure\Models\Session;
use App\Modules\Analytics\Infrastructure\Models\SourceDaily;
use App\Modules\Analytics\Infrastructure\Models\Visitor;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Content\Infrastructure\Models\Post;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

/**
 * AnalyticsReportRepository — read-only реализация отчётов аналитики.
 *
 * Считает метрики напрямую из append-only таблиц analytics_* и агрегатов
 * (analytics_sources_daily). Результат — ассоциативные массивы для Inertia.
 */
class AnalyticsReportRepository implements AnalyticsReportRepositoryInterface
{
    public function dashboard(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return [
            'totals' => $this->dashboardTotals($from, $to),
            'daily' => $this->dailyActivity($from, $to),
            'devices' => $this->deviceBreakdown($from, $to),
            'sources' => $this->sourceBreakdown($from, $to),
        ];
    }

    public function search(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $top = Search::query()
            ->whereBetween('searched_at', [$from, $to])
            ->where('is_bot', false)
            ->select('query_normalized')
            ->selectRaw('MIN(query) as query_sample')
            ->selectRaw('COUNT(*) as searches_count')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors')
            ->selectRaw('SUM(CASE WHEN clicked_result_id IS NOT NULL THEN 1 ELSE 0 END) as clicks')
            ->selectRaw('AVG(CASE WHEN clicked_position IS NOT NULL THEN clicked_position END) as avg_position')
            ->groupBy('query_normalized')
            ->orderByDesc('searches_count')
            ->limit(50)
            ->get()
            ->map(fn($row) => $this->mapSearchRow($row))
            ->all();

        return [
            'totals' => [
                'searches' => Search::whereBetween('searched_at', [$from, $to])->where('is_bot', false)->count(),
                'with_clicks' => Search::whereBetween('searched_at', [$from, $to])->where('is_bot', false)->whereNotNull('clicked_result_id')->count(),
                'avg_click_position' => $this->avgClickPosition($from, $to),
            ],
            'top' => $top,
            'no_results' => $this->noResultQueries($from, $to, 30),
            'low_ctr' => $this->lowCtrQueries($top),
        ];
    }

    public function pages(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $duration = PageView::whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false)
            ->whereNotNull('duration')
            ->avg('duration');

        $scroll = PageView::whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false)
            ->whereNotNull('scroll_depth')
            ->avg('scroll_depth');

        return [
            'metrics' => [
                'views' => PageView::whereBetween('viewed_at', [$from, $to])->where('is_bot', false)->count(),
                'avg_duration' => $duration !== null ? (int) round((float) $duration) : null,
                'avg_scroll_depth' => $scroll !== null ? (int) round((float) $scroll) : null,
            ],
            'top_products' => $this->topEntityPages($from, $to, 'product', 20),
            'top_categories' => $this->topEntityPages($from, $to, 'category', 20),
            'top_posts' => $this->topEntityPages($from, $to, 'post', 20),
            'high_bounce' => $this->highBouncePages($from, $to, 20),
            'entry_pages' => $this->entryExitPages($from, $to, true, 20),
            'exit_pages' => $this->entryExitPages($from, $to, false, 20),
        ];
    }

    public function actions(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $totals = Action::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->where('is_bot', false)
            ->select('action_type')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors')
            ->selectRaw('COUNT(DISTINCT entity_id) as entities')
            ->groupBy('action_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'action_type' => $row->action_type,
                'total' => (int) $row->total,
                'unique_visitors' => (int) $row->unique_visitors,
                'entities' => (int) $row->entities,
            ])
            ->all();

        return [
            'totals' => $totals,
            'cart_adds' => $this->topActionEntities($from, $to, 'cart_add', 'product', 20),
            'cart_removes' => $this->topActionEntities($from, $to, 'cart_remove', 'product', 20),
            'wishlist_adds' => $this->topActionEntities($from, $to, 'wishlist_add', 'product', 20),
            'banner_clicks' => $this->topActionEntities($from, $to, 'banner_click', 'banner', 20),
        ];
    }

    public function sources(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $rows = SourceDaily::query()
            ->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->select('source')
            ->selectRaw('SUM(sessions_count) as sessions')
            ->selectRaw('SUM(unique_visitors_count) as unique_visitors')
            ->selectRaw('SUM(new_visitors_count) as new_visitors')
            ->selectRaw('SUM(bounce_count) as bounces')
            ->selectRaw('AVG(avg_duration) as avg_duration')
            ->groupBy('source')
            ->orderByDesc('sessions')
            ->get()
            ->map(fn($row) => [
                'source' => $row->source,
                'sessions' => (int) $row->sessions,
                'unique_visitors' => (int) $row->unique_visitors,
                'new_visitors' => (int) $row->new_visitors,
                'bounces' => (int) $row->bounces,
                'avg_duration' => $row->avg_duration !== null ? (int) round((float) $row->avg_duration) : null,
                'conversion' => 0.0,
            ])
            ->keyBy('source');

        foreach ($this->sourcesConversion($from, $to) as $conversion) {
            $source = $conversion['source'];
            if (!isset($rows[$source])) {
                continue;
            }

            $sessions = (int) $rows[$source]['sessions'];
            $orders = (int) $conversion['orders'];

            $rows[$source]['conversion'] = $sessions > 0
                ? round($orders / $sessions * 100, 1)
                : 0.0;
        }

        return [
            'sources' => array_values($rows->all()),
            'utm_campaigns' => $this->utmCampaigns($from, $to, 20),
        ];
    }

    public function audience(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $devices = Visitor::query()
            ->whereBetween('first_visit_at', [$from, $to])
            ->select('device_type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => ['name' => $row->device_type ?: 'unknown', 'total' => (int) $row->total])
            ->all();

        $os = Visitor::query()
            ->whereBetween('first_visit_at', [$from, $to])
            ->select('os')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('os')
            ->groupBy('os')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn($row) => ['name' => $row->os, 'total' => (int) $row->total])
            ->all();

        $browsers = Visitor::query()
            ->whereBetween('first_visit_at', [$from, $to])
            ->select('browser')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn($row) => ['name' => $row->browser, 'total' => (int) $row->total])
            ->all();

        return [
            'cities' => $this->geography($from, $to, 'city', 20),
            'regions' => $this->geography($from, $to, 'region', 20),
            'countries' => $this->geography($from, $to, 'country', 20),
            'devices' => $devices,
            'os' => $os,
            'browsers' => $browsers,
            'new_vs_returning' => $this->newVsReturning($from, $to),
        ];
    }

    public function funnel(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $pageViews = PageView::whereBetween('viewed_at', [$from, $to])->where('is_bot', false)->distinct()->count('session_id');
        $cartAdds = Action::where('action_type', 'cart_add')->whereBetween('occurred_at', [$from, $to])->where('is_bot', false)->distinct()->count('session_id');
        $orders = Action::where('action_type', 'order_placed')->whereBetween('occurred_at', [$from, $to])->where('is_bot', false)->distinct()->count('session_id');

        $cartSessions = Action::where('action_type', 'cart_add')->whereBetween('occurred_at', [$from, $to])->where('is_bot', false)->distinct()->pluck('session_id');
        $orderSessions = Action::where('action_type', 'order_placed')->whereBetween('occurred_at', [$from, $to])->where('is_bot', false)->distinct()->pluck('session_id');

        return [
            'stages' => [
                ['stage' => 'page_view', 'sessions' => (int) $pageViews],
                ['stage' => 'cart_add', 'sessions' => (int) $cartAdds],
                ['stage' => 'order_placed', 'sessions' => (int) $orders],
            ],
            'abandoned_carts' => $cartSessions->diff($orderSessions)->count(),
            'top_paths' => $this->topPaths($from, $to, 20),
        ];
    }

    // =====================================================================
    // Dashboard
    // =====================================================================

    private function dashboardTotals(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $sessions = Session::whereBetween('started_at', [$from, $to])->where('is_bot', false);

        $bounceRate = (clone $sessions)
            ->selectRaw('AVG(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounce_rate')
            ->first();

        $avgDuration = (clone $sessions)->whereNotNull('duration')->avg('duration');

        return [
            'visitors' => (clone $sessions)->distinct()->count('visitor_id'),
            'sessions' => (clone $sessions)->count(),
            'page_views' => PageView::whereBetween('viewed_at', [$from, $to])->where('is_bot', false)->count(),
            'actions' => Action::whereBetween('occurred_at', [$from, $to])->where('is_bot', false)->count(),
            'searches' => Search::whereBetween('searched_at', [$from, $to])->where('is_bot', false)->count(),
            'bounce_rate' => $bounceRate->bounce_rate !== null ? round((float) $bounceRate->bounce_rate * 100, 1) : 0.0,
            'avg_session_duration' => $avgDuration !== null ? (int) round((float) $avgDuration) : null,
        ];
    }

    private function dailyActivity(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $sessions = Session::query()
            ->whereBetween('started_at', [$from, $to])
            ->where('is_bot', false)
            ->selectRaw('DATE(started_at) as d, COUNT(*) as sessions')
            ->groupBy('d')
            ->pluck('sessions', 'd');

        $visitors = Session::query()
            ->whereBetween('started_at', [$from, $to])
            ->where('is_bot', false)
            ->selectRaw('DATE(started_at) as d, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('d')
            ->pluck('visitors', 'd');

        $views = PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false)
            ->selectRaw('DATE(viewed_at) as d, COUNT(*) as views')
            ->groupBy('d')
            ->pluck('views', 'd');

        $actions = Action::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->where('is_bot', false)
            ->selectRaw('DATE(occurred_at) as d, COUNT(*) as actions')
            ->groupBy('d')
            ->pluck('actions', 'd');

        $searches = Search::query()
            ->whereBetween('searched_at', [$from, $to])
            ->where('is_bot', false)
            ->selectRaw('DATE(searched_at) as d, COUNT(*) as searches')
            ->groupBy('d')
            ->pluck('searches', 'd');

        $dates = array_unique(array_merge(
            $sessions->keys()->all(),
            $visitors->keys()->all(),
            $views->keys()->all(),
            $actions->keys()->all(),
            $searches->keys()->all(),
        ));
        sort($dates);

        return array_map(fn(string $date) => [
            'date' => $date,
            'sessions' => (int) ($sessions[$date] ?? 0),
            'visitors' => (int) ($visitors[$date] ?? 0),
            'page_views' => (int) ($views[$date] ?? 0),
            'actions' => (int) ($actions[$date] ?? 0),
            'searches' => (int) ($searches[$date] ?? 0),
        ], $dates);
    }

    private function deviceBreakdown(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return Session::query()
            ->whereBetween('started_at', [$from, $to])
            ->where('is_bot', false)
            ->select('device_type')
            ->selectRaw('COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('device_type')
            ->orderByDesc('sessions')
            ->get()
            ->map(fn($row) => [
                'name' => $row->device_type ?: 'unknown',
                'sessions' => (int) $row->sessions,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    private function sourceBreakdown(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return Session::query()
            ->whereBetween('started_at', [$from, $to])
            ->where('is_bot', false)
            ->select('source')
            ->selectRaw('COUNT(*) as sessions, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('source')
            ->orderByDesc('sessions')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'name' => $row->source ?: 'direct',
                'sessions' => (int) $row->sessions,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    // =====================================================================
    // Search
    // =====================================================================

    private function mapSearchRow($row): array
    {
        $searches = (int) $row->searches_count;
        $clicks = (int) $row->clicks;

        return [
            'query' => $row->query_sample ?? $row->query_normalized,
            'searches' => $searches,
            'unique_visitors' => (int) $row->unique_visitors,
            'clicks' => $clicks,
            'ctr' => $searches > 0 ? round($clicks / $searches * 100, 1) : 0.0,
            'avg_position' => $row->avg_position !== null ? round((float) $row->avg_position, 1) : null,
        ];
    }

    private function noResultQueries(DateTimeImmutable $from, DateTimeImmutable $to, int $limit): array
    {
        return Search::query()
            ->whereBetween('searched_at', [$from, $to])
            ->where('is_bot', false)
            ->where('results_count', 0)
            ->select('query_normalized')
            ->selectRaw('MIN(query) as query_sample, COUNT(*) as total')
            ->groupBy('query_normalized')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'query' => $row->query_sample ?? $row->query_normalized,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $top
     */
    private function lowCtrQueries(array $top): array
    {
        $low = array_values(array_filter($top, fn(array $row) => $row['searches'] >= 5 && $row['ctr'] < 10.0));

        usort($low, fn(array $a, array $b) => $a['ctr'] <=> $b['ctr']);

        return array_slice($low, 0, 20);
    }

    private function avgClickPosition(DateTimeImmutable $from, DateTimeImmutable $to): ?float
    {
        $avg = Search::whereBetween('searched_at', [$from, $to])
            ->where('is_bot', false)
            ->whereNotNull('clicked_position')
            ->avg('clicked_position');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    // =====================================================================
    // Pages
    // =====================================================================

    private function topEntityPages(DateTimeImmutable $from, DateTimeImmutable $to, string $pageType, int $limit): array
    {
        $rows = PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false)
            ->where('page_type', $pageType)
            ->whereNotNull('entity_id')
            ->select('entity_id')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors')
            ->selectRaw('AVG(duration) as avg_duration')
            ->selectRaw('AVG(scroll_depth) as avg_scroll_depth')
            ->groupBy('entity_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'entity_id' => (int) $row->entity_id,
                'name' => '',
                'views' => (int) $row->views,
                'unique_visitors' => (int) $row->unique_visitors,
                'avg_duration' => $row->avg_duration !== null ? (int) round((float) $row->avg_duration) : null,
                'avg_scroll_depth' => $row->avg_scroll_depth !== null ? (int) round((float) $row->avg_scroll_depth) : null,
            ])
            ->all();

        $names = $this->entityNames($pageType, array_column($rows, 'entity_id'));

        foreach ($rows as $index => $row) {
            $rows[$index]['name'] = $names[$row['entity_id']] ?? ('#' . $row['entity_id']);
        }

        return $rows;
    }

    /**
     * @param int[] $ids
     * @return array<int, string>
     */
    private function entityNames(string $pageType, array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $query = match ($pageType) {
            'product' => Product::withTrashed()->whereIn('id', $ids),
            'category' => Category::whereIn('id', $ids),
            'post' => Post::whereIn('id', $ids),
            default => null,
        };

        if ($query === null) {
            return [];
        }

        return $query->pluck('name', 'id')->all();
    }

    private function highBouncePages(DateTimeImmutable $from, DateTimeImmutable $to, int $limit): array
    {
        return PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false)
            ->where('is_bounce', true)
            ->select('page_type', 'entity_id')
            ->selectRaw('COUNT(*) as bounces')
            ->groupBy('page_type', 'entity_id')
            ->orderByDesc('bounces')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'page_type' => $row->page_type,
                'entity_id' => $row->entity_id !== null ? (int) $row->entity_id : null,
                'bounces' => (int) $row->bounces,
            ])
            ->all();
    }

    private function entryExitPages(DateTimeImmutable $from, DateTimeImmutable $to, bool $entry, int $limit): array
    {
        $query = PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->where('is_bot', false);

        if ($entry) {
            $query->where('is_entry', true);
        } else {
            $query->where('is_exit', true);
        }

        return $query
            ->select('page_type', 'entity_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page_type', 'entity_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'page_type' => $row->page_type,
                'entity_id' => $row->entity_id !== null ? (int) $row->entity_id : null,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    // =====================================================================
    // Actions
    // =====================================================================

    private function topActionEntities(DateTimeImmutable $from, DateTimeImmutable $to, string $actionType, string $entityType, int $limit): array
    {
        return Action::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->where('is_bot', false)
            ->where('action_type', $actionType)
            ->where('entity_type', $entityType)
            ->whereNotNull('entity_id')
            ->select('entity_id')
            ->selectRaw('COUNT(*) as total, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('entity_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'entity_id' => (int) $row->entity_id,
                'total' => (int) $row->total,
                'unique_visitors' => (int) $row->unique_visitors,
            ])
            ->all();
    }

    // =====================================================================
    // Sources
    // =====================================================================

    private function utmCampaigns(DateTimeImmutable $from, DateTimeImmutable $to, int $limit): array
    {
        return SourceDaily::query()
            ->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->whereNotNull('utm_campaign')
            ->where('utm_campaign', '!=', '')
            ->select('utm_campaign', 'utm_source')
            ->selectRaw('SUM(sessions_count) as sessions, SUM(unique_visitors_count) as visitors')
            ->groupBy('utm_campaign', 'utm_source')
            ->orderByDesc('sessions')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'campaign' => $row->utm_campaign,
                'source' => $row->utm_source,
                'sessions' => (int) $row->sessions,
                'visitors' => (int) $row->visitors,
            ])
            ->all();
    }

    private function sourcesConversion(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return DB::table('analytics_actions as a')
            ->join('analytics_sessions as s', 's.id', '=', 'a.session_id')
            ->where('a.action_type', 'order_placed')
            ->where('a.is_bot', false)
            ->whereBetween('a.occurred_at', [$from, $to])
            ->select('s.source')
            ->selectRaw('COUNT(DISTINCT a.session_id) as orders')
            ->groupBy('s.source')
            ->get()
            ->map(fn($row) => [
                'source' => $row->source ?: 'direct',
                'orders' => (int) $row->orders,
            ])
            ->all();
    }

    // =====================================================================
    // Audience
    // =====================================================================

    private function geography(DateTimeImmutable $from, DateTimeImmutable $to, string $field, int $limit): array
    {
        return Visitor::query()
            ->whereBetween('first_visit_at', [$from, $to])
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->select($field)
            ->selectRaw('COUNT(*) as total')
            ->groupBy($field)
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($row) => ['name' => $row->{$field}, 'total' => (int) $row->total])
            ->all();
    }

    private function newVsReturning(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $visitorIds = Session::whereBetween('started_at', [$from, $to])
            ->where('is_bot', false)
            ->distinct()
            ->pluck('visitor_id');

        $new = Visitor::whereIn('id', $visitorIds)
            ->whereBetween('first_visit_at', [$from, $to])
            ->count();

        $returning = Visitor::whereIn('id', $visitorIds)
            ->where('first_visit_at', '<', $from)
            ->count();

        return [
            ['name' => 'new', 'total' => (int) $new],
            ['name' => 'returning', 'total' => (int) $returning],
        ];
    }

    // =====================================================================
    // Funnel
    // =====================================================================

    private function topPaths(DateTimeImmutable $from, DateTimeImmutable $to, int $limit): array
    {
        $rows = Path::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->orderBy('step_number')
            ->get(['session_id', 'page_type', 'step_number']);

        $paths = [];
        foreach ($rows as $row) {
            if (count($paths[$row->session_id] ?? []) >= 8) {
                continue;
            }
            $paths[$row->session_id][] = $row->page_type;
        }

        $counts = [];
        foreach ($paths as $path) {
            $key = implode(' → ', $path);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        arsort($counts);

        $result = [];
        foreach (array_slice($counts, 0, $limit, true) as $path => $count) {
            $result[] = ['path' => $path, 'count' => (int) $count];
        }

        return $result;
    }
}
