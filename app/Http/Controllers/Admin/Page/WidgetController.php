<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

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
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $widgets = Widget::get();
            return view('admin.page.widget.index', compact('widgets'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            $templates = Widget::WIDGET_TEMPLATES;
            return view('admin.page.widget.create', compact('templates'));
        });
    }

    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $widget = $this->service->create($request);
            return view('admin.page.widget.show', compact('widget'));
        });
    }

    public function show(Widget $widget)
    {
        return $this->try_catch_admin(function () use($widget) {
            return view('admin.page.widget.show', compact('widget'));
        });
    }

    public function edit(Widget $widget)
    {
        return $this->try_catch_admin(function () use($widget) {
            $templates = Widget::WIDGET_TEMPLATES;
            return view('admin.page.widget.edit', compact('widget', 'templates'));
        });
    }

    public function update(Request $request, Widget $widget)
    {
        return $this->try_catch_admin(function () use($request, $widget) {
            $widget = $this->service->update($request, $widget);
            return view('admin.page.widget.show', compact('widget'));
        });
    }

    public function destroy(Widget $widget)
    {
        return $this->try_catch_admin(function () use($widget) {
            $this->service->destroy($widget);
            return redirect()->route('admin.page.widget.index');
        });
    }

    public function draft(Widget $widget)
    {
        return $this->try_catch_admin(function () use($widget) {
            $widget->draft();
            return redirect()->route('admin.page.widget.show', compact('widget'));
        });
    }

    public function activated(Widget $widget)
    {
        return $this->try_catch_admin(function () use($widget) {
            $widget->activated();
            return redirect()->route('admin.page.widget.show', compact('widget'));
        });
    }

    //AJAX
    public function get_ids(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $class = $request['class'];
            $result = $this->service->getIds($class);
            return response()->json($result);
        });
    }


}
