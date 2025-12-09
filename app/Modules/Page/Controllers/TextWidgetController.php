<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Widgets\TextWidget;
use App\Modules\Page\Entity\Widgets\TextWidgetItem;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Repository\TextWidgetRepository;
use App\Modules\Page\Service\TextWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TextWidgetController extends Controller
{

    private TextWidgetService $service;
    private TemplateRepository $templates;
    private TextWidgetRepository $repository;

    public function __construct(
        TextWidgetService    $service,
        TemplateRepository     $templates,
        TextWidgetRepository $repository,
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
        $templates = $this->templates->getTemplates('text');

        return Inertia::render('Page/Widget/Text/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.page.widget.text.show', $widget)->with('success', 'Текстовый виджет сохранен');
    }

    public function show(TextWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('text');

        return Inertia::render('Page/Widget/Text/Show', [
            'widget' => $this->repository->TextWithToArray($widget),
            'templates' => $templates,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function set_widget(TextWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(TextWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Текстовый виджет удален');
    }

    public function add_item(TextWidget $widget, Request $request): RedirectResponse
    {
        $this->service->addItem($widget, $request);
        return redirect()->back()->with('success', 'Элемент добавлен');
    }

    public function set_item(TextWidgetItem $item, Request $request): RedirectResponse
    {
        $this->service->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_item(TextWidgetItem $item): RedirectResponse
    {
        $this->service->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(TextWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function up_item(TextWidgetItem $item): RedirectResponse
    {
        $this->service->upItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down_item(TextWidgetItem $item): RedirectResponse
    {
        $this->service->downItem($item);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
