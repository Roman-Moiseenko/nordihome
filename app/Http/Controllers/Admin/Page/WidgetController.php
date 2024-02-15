<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Events\ThrowableHasAppeared;
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
        try {
            $widgets = Widget::get();
            return view('admin.page.widget.index', compact('widgets'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create()
    {
        try {
            $templates = Widget::WIDGET_TEMPLATES;
            return view('admin.page.widget.create', compact('templates'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        try {
            $widget = $this->service->create($request);
            return view('admin.page.widget.show', compact('widget'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Widget $widget)
    {
        try {
            return view('admin.page.widget.show', compact('widget'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Widget $widget)
    {
        try {
            $templates = Widget::WIDGET_TEMPLATES;
            return view('admin.page.widget.edit', compact('widget', 'templates'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Widget $widget)
    {
        try {
            $widget = $this->service->update($request, $widget);
            return view('admin.page.widget.show', compact('widget'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Widget $widget)
    {
        try {
            $this->service->destroy($widget);
            return redirect()->route('admin.page.widget.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function draft(Widget $widget)
    {
        try {
            $widget->draft();

            return redirect()->route('admin.page.widget.show', compact('widget'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function activated(Widget $widget)
    {
        try {
            $widget->activated();

            return redirect()->route('admin.page.widget.show', compact('widget'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    //AJAX
    public function get_ids(Request $request)
    {
        try {
            $class = $request['class'];
            $result = $this->service->getIds($class);
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
            event(new ThrowableHasAppeared($e));

        }

        return response()->json($result);
    }


}
