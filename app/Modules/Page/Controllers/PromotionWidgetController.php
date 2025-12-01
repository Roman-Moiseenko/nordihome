<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\PromotionWidget;
use App\Modules\Page\Repository\PromotionWidgetRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\PromotionWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromotionWidgetController extends Controller
{

    private TemplateRepository $templates;
    private PromotionWidgetRepository $repository;
    private PromotionWidgetService $service;

    public function __construct(
        PromotionWidgetRepository $repository,
        TemplateRepository        $templates,
        PromotionWidgetService $service,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);

        $this->templates = $templates;
        $this->repository = $repository;
        $this->service = $service;
    }


    public function index(Request $request): Response
    {
        $widgets = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('promotion');

        return Inertia::render('Page/Widget/Promotion/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.page.widget.promotion.show', $widget)->with('success', 'Виджет акции сохранен');
    }

    public function show(PromotionWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('promotion');
        $banners = BannerWidget::orderBy('name')->getModels();
        $promotions = Promotion::orderBy('name')->where('active', true)->getModels();

        return Inertia::render('Page/Widget/Promotion/Show', [
            'widget' => $this->repository->PromotionWithToArray($widget),
            'templates' => $templates,
            'banners' => $banners,
            'promotions' => $promotions,
        ]);
    }

    public function toggle(PromotionWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function set_widget(PromotionWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(PromotionWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Виджет акции удален');
    }

}
