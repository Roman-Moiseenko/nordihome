<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Service\WidgetService;
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
        return view('admin.page.widget.show', compact('widget'));
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
        try {
            $class = $request['class'];
            $result = $this->service->getIds($class);
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }

        return response()->json($result);
    }


}
