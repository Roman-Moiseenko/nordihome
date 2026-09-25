<?php
declare(strict_types=1);

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Domain\ValueObjects\ProductGroupType;
use App\Modules\Content\Entity\Widgets\BannerWidget;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\ProductWidgetItem;
use App\Modules\Content\Repository\ProductWidgetRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\ProductWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class ProductWidgetController extends Controller
{

    private ProductWidgetService $service;
    private TemplateRepository $templates;
    private ProductWidgetRepository $repository;

    public function __construct(
        ProductWidgetService    $service,
        TemplateRepository      $templates,
        ProductWidgetRepository $repository,
    )
    {
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $templates = $this->templates->getTemplates('product');
        $widgets = $this->repository->getIndex($request);
        return Inertia::render('Content/Widget/Product/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->service->create($request);
        return redirect()->back()->with('success', 'Виджет сохранен');
        //route('admin.content.widget.product.show', $widget)
    }


    public function set_widget(Request $request, ProductWidget $widget): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(ProductWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Виджет удален');
    }

    public function toggle(ProductWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

}
