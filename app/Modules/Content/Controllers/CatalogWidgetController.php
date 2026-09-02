<?php
declare(strict_types=1);

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Entity\Widgets\BannerWidget;
use App\Modules\Content\Entity\Widgets\BannerWidgetItem;
use App\Modules\Content\Entity\Widgets\CatalogWidget;
use App\Modules\Content\Entity\Widgets\CatalogWidgetItem;
use App\Modules\Content\Repository\BannerWidgetRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\BannerWidgetService;
use App\Modules\Content\Service\CatalogWidgetService;
use App\Modules\Shared\Application\Actions\UploadPhotoUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogWidgetController extends Controller
{
    private TemplateRepository $templates;
  //  private BannerWidgetRepository $repository;
    //private BannerWidgetService $service;

    public function __construct(

        TemplateRepository     $templates,

        BannerWidgetRepository $repository,
        private CatalogWidgetService    $service,
    )
    {
        $this->templates = $templates;
    }

    public function index(Request $request): Response
    {
        $widgets = CatalogWidget::orderBy('name')
            ->get()
            ->map(fn(CatalogWidget $widget) => array_merge($widget->toArray(), [
                'count' => $widget->items()->count(),
            ]));// $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('catalog');

        return Inertia::render('Content/Widget/Catalog/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget =  CatalogWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
        return redirect()->route('admin.content.widget.catalog.show', $widget)->with('success', 'Виджет сохранен');
    }

    public function show(CatalogWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('catalog');
        $widget = array_merge($widget->toArray(), [
            'items' => $widget->items()->get()->map(fn(CatalogWidgetItem $item) => array_merge($item->toArray(), [
                'image' => $item->image(),
                'url' => $item->url(),
                'name' => $item->name(),
            ])),

        ]);

        return Inertia::render('Content/Widget/Catalog/Show', [
            'widget' => $widget,
            'templates' => $templates,
        ]);
    }

    public function setWidget(CatalogWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(CatalogWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Баннер удален');
    }

    public function addItem(CatalogWidget $widget, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $this->service->addItem($widget->id, $request);

        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function setItem(CatalogWidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function delItem(CatalogWidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(CatalogWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function upItem(CatalogWidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function downItem(CatalogWidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
