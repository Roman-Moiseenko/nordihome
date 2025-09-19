<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\BannerWidgetItem;
use App\Modules\Page\Entity\Template;
use App\Modules\Page\Repository\BannerWidgetRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\BannerWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerWidgetController extends Controller
{
    private TemplateRepository $templates;
    private BannerWidgetRepository $repository;
    private BannerWidgetService $service;

    public function __construct(
        BannerWidgetService    $service,
        TemplateRepository     $templates,
        BannerWidgetRepository $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $widgets = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Widget/Banner/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.page.widget.banner.show', $widget)->with('success', 'Баннер сохранен');
    }

    public function show(BannerWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('banner');

        return Inertia::render('Page/Widget/Banner/Show', [
            'widget' => $this->repository->BannerWithToArray($widget),
            'templates' => $templates,
        ]);
    }

    public function set_widget(BannerWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setBanner($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(BannerWidget $widget): RedirectResponse
    {
        $this->service->delBanner($widget);
        return redirect()->back()->with('success', 'Баннер удален');
    }

    public function add_item(BannerWidget $widget, Request $request): RedirectResponse
    {
        $this->service->addItem($widget, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(BannerWidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(BannerWidget $widget): RedirectResponse
    {
        if ($widget->isActive()) {
            $message = 'Баннер убран из показа';
        } else {
            $message = 'Баннер добавлен в показы';
        }
        $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function up_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(BannerWidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
