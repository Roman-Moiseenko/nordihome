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
        $promotions = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('promotion');

        return Inertia::render('Page/Widget/Promotion/Index', [
            'promotions' => $promotions,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $promotion = $this->service->create($request);
        return redirect()->route('admin.page.widget.promotion.show', $promotion)->with('success', 'Виджет акции сохранен');
    }

    public function show(PromotionWidget $promotion): Response
    {
        $templates = $this->templates->getTemplates('promotion');
        $banners = BannerWidget::orderBy('name')->getModels();
        $promotions = Promotion::orderBy('name')->where('active', true)->getModels();

        return Inertia::render('Page/Widget/Promotion/Show', [
            'promotion' => $this->repository->PromotionWithToArray($promotion),
            'templates' => $templates,
            'banners' => $banners,
            'promotions' => $promotions,
        ]);
    }

    public function toggle(PromotionWidget $promotion): RedirectResponse
    {
        if ($promotion->isActive()) {
            $message = 'Виджет акции убран из показа';
        } else {
            $message = 'Виджет акции добавлен в показы';
        }
        $this->service->toggle($promotion);
        return redirect()->back()->with('success', $message);
    }

    public function set_widget(PromotionWidget $promotion, Request $request): RedirectResponse
    {
        $this->service->setWidget($promotion, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(PromotionWidget $promotion): RedirectResponse
    {
        $this->service->delWidget($promotion);
        return redirect()->back()->with('success', 'Виджет акции удален');
    }

}
