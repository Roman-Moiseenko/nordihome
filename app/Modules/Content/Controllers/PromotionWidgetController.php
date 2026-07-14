<?php

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Content\Entity\Widgets\BannerWidget;
use App\Modules\Content\Entity\Widgets\PromotionWidget;
use App\Modules\Content\Repository\PromotionWidgetRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\PromotionWidgetService;
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
        $this->templates = $templates;
        $this->repository = $repository;
        $this->service = $service;
    }


    public function index(Request $request): Response
    {
        $widgets = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('promotion');

        return Inertia::render('Content/Widget/Promotion/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.content.widget.promotion.show', $widget)->with('success', 'Виджет акции сохранен');
    }

    public function show(PromotionWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('promotion');
        $banners = BannerWidget::orderBy('name')->getModels();
        $promotions = Promotion::orderBy('name')->where('active', true)->getModels();

        return Inertia::render('Content/Widget/Promotion/Show', [
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
