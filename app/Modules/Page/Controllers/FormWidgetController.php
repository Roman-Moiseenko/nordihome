<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Widgets\FormWidget;
use App\Modules\Page\Repository\FormWidgetRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\FormWidgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormWidgetController extends Controller
{
    private FormWidgetService $service;
    private TemplateRepository $templates;
    private FormWidgetRepository $repository;

    public function __construct(
        FormWidgetService    $service,
        TemplateRepository      $templates,
        FormWidgetRepository $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $templates = $this->templates->getTemplates('form');
        $widgets = $this->repository->getIndex($request);
        return Inertia::render('Page/Widget/Form/Index', [
            'widgets' => $widgets,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $widget = $this->service->create($request);
        return redirect()->route('admin.page.widget.form.show', $widget)->with('success', 'Виджет сохранен');
    }

    public function show(FormWidget $widget): Response
    {
        $templates = $this->templates->getTemplates('form');
        return Inertia::render('Page/Widget/Form/Show', [
            'widget' => $this->repository->WidgetWithToArray($widget),
            'templates' => $templates,
        ]);
    }

    public function set_widget(Request $request, FormWidget $widget): RedirectResponse
    {
        $this->service->setWidget($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(FormWidget $widget): RedirectResponse
    {
        $this->service->delWidget($widget);
        return redirect()->back()->with('success', 'Виджет удален');
    }

    public function toggle(FormWidget $widget): RedirectResponse
    {
        $message = $this->service->toggle($widget);
        return redirect()->back()->with('success', $message);
    }

    public function set_fields(FormWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setFields($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_lists(FormWidget $widget, Request $request): RedirectResponse
    {
        $this->service->setLists($widget, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }


}
