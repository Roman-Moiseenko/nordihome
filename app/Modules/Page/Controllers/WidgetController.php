<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Entity\WidgetItem;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Repository\WidgetRepository;
use App\Modules\Page\Service\WidgetService;
use App\Modules\Product\Entity\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class WidgetController extends Controller
{

    private WidgetService $service;
    private TemplateRepository $templates;
    private WidgetRepository $repository;

    public function __construct(
        WidgetService      $service,
        TemplateRepository $templates,
        WidgetRepository   $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $templates = $this->templates->getTemplates('widget');
        $widgets = $this->repository->getIndex($request);
        return Inertia::render('Page/Widget/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.page.widget.show', $widget)->with('success', 'Виджет сохранен');
    }

    public function show(Widget $widget): Response
    {
        $groups = $this->repository->getGroups($widget);

        $banners = Banner::orderBy('name')->getModels();
        $templates = $this->templates->getTemplates('widget');
        return Inertia::render('Page/Widget/Show', [
            'widget' => $this->repository->WidgetWithToArray($widget),
            'templates' => $templates,
            'groups' => $groups,
            'banners' => $banners,
        ]);

        //return view('admin.page.widget.show', compact('widget'));
    }

    public function set_widget(Request $request, Widget $widget)
    {
        $this->service->setWidget($request, $widget);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Widget $widget)
    {
        $this->service->destroy($widget);
        return redirect()->back()->with('success', 'Виджет удален');
    }

    public function toggle(Widget $widget): RedirectResponse
    {
        if ($widget->isActive()) {
            $message = 'Виджет убран из показа';
        } else {
            $message = 'Виджет добавлен в показы';
        }
        $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function add_item(Widget $widget, Request $request): RedirectResponse
    {
        $this->service->addItem($widget, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(WidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(WidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function up_item(WidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(WidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
