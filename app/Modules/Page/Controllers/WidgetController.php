<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Service\WidgetService;
use Illuminate\Http\Request;


class WidgetController extends Controller
{

    private WidgetService $service;

    public function __construct(WidgetService $service)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $widgets = Widget::get();
        return view('admin.page.widget.index', compact('widgets'));
    }

    public function create()
    {
        $templates = Widget::WIDGET_TEMPLATES;
        return view('admin.page.widget.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $widget = $this->service->create($request);
        return view('admin.page.widget.show', compact('widget'));
    }

    public function show(Widget $widget)
    {
        return view('admin.page.widget.show', compact('widget'));
    }

    public function edit(Widget $widget)
    {
        $templates = Widget::WIDGET_TEMPLATES;
        return view('admin.page.widget.edit', compact('widget', 'templates'));
    }

    public function update(Request $request, Widget $widget)
    {
        $widget = $this->service->update($request, $widget);
        return redirect()->route('admin.page.widget.show', $widget);
    }

    public function destroy(Widget $widget)
    {
        $this->service->destroy($widget);
        return redirect()->route('admin.page.widget.index');
    }

    public function draft(Widget $widget)
    {
        $widget->draft();
        return redirect()->route('admin.page.widget.show', compact('widget'));
    }

    public function activated(Widget $widget)
    {
        $widget->activated();
        return redirect()->route('admin.page.widget.show', compact('widget'));
    }

    //AJAX
    public function get_ids(Request $request)
    {
        $class = $request->string('class')->value();
        $result = $this->service->getIds($class);
        return response()->json($result);
    }

}
