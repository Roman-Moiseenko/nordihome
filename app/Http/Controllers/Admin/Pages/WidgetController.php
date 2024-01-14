<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Pages;

use App\Modules\Pages\Entity\Widget;
use App\Modules\Pages\Service\WidgetService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WidgetController extends Controller
{

    private WidgetService $service;

    public function __construct(WidgetService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $widgets = Widget::get();
        return view('admin.pages.widget.index', compact('widgets'));
    }

    public function create()
    {
        $templates = Widget::WIDGET_TEMPLATES;
        return view('admin.pages.widget.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $widget = $this->service->create($request);
        return view('admin.pages.widget.show', compact('widget'));
    }

    public function show(Widget $widget)
    {
        return view('admin.pages.widget.show', compact('widget'));
    }

    public function edit(Widget $widget)
    {
        $templates = Widget::WIDGET_TEMPLATES;
        return view('admin.pages.widget.edit', compact('widget', 'templates'));
    }

    public function update(Request $request, Widget $widget)
    {
        $widget = $this->service->update($request, $widget);
        return view('admin.pages.widget.show', compact('widget'));
    }

    public function destroy(Widget $widget)
    {

        $this->service->destroy($widget);
        return redirect()->route('admin.pages.widget.index');
    }

    public function draft(Widget $widget)
    {
        $widget->draft();

        return redirect()->route('admin.pages.widget.show', compact('widget'));
    }

    public function activated(Widget $widget)
    {
        $widget->activated();

        return redirect()->route('admin.pages.widget.show', compact('widget'));
    }

    //AJAX
    public function get_ids(Request $request)
    {
        try {
            $class = $request['class'];
            $result = $this->service->getIds($class);
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }

        return response()->json($result);
    }


}
