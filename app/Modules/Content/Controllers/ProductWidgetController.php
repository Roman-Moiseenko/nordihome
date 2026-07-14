<?php
declare(strict_types=1);

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
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
        $widget = $this->service->create($request);
        return redirect()->route('admin.content.widget.product.show', $widget)->with('success', 'Виджет сохранен');
    }

    public function show(ProductWidget $widget): Response
    {
        $groups = $this->repository->getGroups($widget);

        $banners = BannerWidget::orderBy('name')->getModels();
        $templates = $this->templates->getTemplates('product');
        return Inertia::render('Content/Widget/Product/Show', [
            'widget' => $this->repository->WidgetWithToArray($widget),
            'templates' => $templates,
            'groups' => $groups,
            'banners' => $banners,
        ]);

        //return view('admin.content.widget.show', compact('widget'));
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

    public function add_item(ProductWidget $widget, Request $request): RedirectResponse
    {
        $this->service->addItem($widget, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(ProductWidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(ProductWidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function up_item(ProductWidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(ProductWidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
