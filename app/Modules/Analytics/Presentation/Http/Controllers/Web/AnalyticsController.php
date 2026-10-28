<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Application\DTOs\Analytics\AnalyticsFilterData;
use App\Modules\Analytics\Application\Queries\Report\GetActionsReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetAudienceReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetDashboardReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetFunnelReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetPagesReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetSearchReportQuery;
use App\Modules\Analytics\Application\Queries\Report\GetSourcesReportQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * AnalyticsController — страницы отчётов аналитики в админке.
 *
 * Контроллер тонкий: валидирует фильтр периода, вызывает Query и передаёт
 * результат на Vue-страницы. Доступ ограничен middleware `role:admin|staff`
 * на уровне маршрутов.
 */
class AnalyticsController extends Controller
{
    public function __construct(
        private readonly GetDashboardReportQuery $dashboardReport,
        private readonly GetSearchReportQuery   $searchReport,
        private readonly GetPagesReportQuery    $pagesReport,
        private readonly GetActionsReportQuery  $actionsReport,
        private readonly GetSourcesReportQuery  $sourcesReport,
        private readonly GetAudienceReportQuery $audienceReport,
        private readonly GetFunnelReportQuery   $funnelReport,
    ) {
    }

    public function dashboard(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Dashboard/Index', [
            'report' => $this->dashboardReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function searches(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Search/Index', [
            'report' => $this->searchReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function pages(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Page/Index', [
            'report' => $this->pagesReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function actions(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Action/Index', [
            'report' => $this->actionsReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function sources(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Source/Index', [
            'report' => $this->sourcesReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function audience(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Audience/Index', [
            'report' => $this->audienceReport->execute($filter),
            'filters' => $filter,
        ]);
    }

    public function funnel(Request $request): Response
    {
        $filter = AnalyticsFilterData::validateAndCreate($request->all());

        return Inertia::render('Analytics/Funnel/Index', [
            'report' => $this->funnelReport->execute($filter),
            'filters' => $filter,
        ]);
    }
}
