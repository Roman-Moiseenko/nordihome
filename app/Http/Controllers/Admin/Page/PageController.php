<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Events\ThrowableHasAppeared;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Service\PageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageController extends Controller
{
    private PageService $service;

    public function __construct(PageService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $pages = Page::get();
            return view('admin.page.page.index', compact('pages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create()
    {
        try {
            $templates = Page::PAGES_TEMPLATES;
            $pages = Page::where('parent_id', null)->get();
            return view('admin.page.page.create', compact('templates', 'pages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'title' => 'required|string|min:6',
            'template' => 'required',
        ]);
        try {
            $page = $this->service->create($request);
            return redirect()->route('admin.page.page.show', $page);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Page $page)
    {
        try {
            return view('admin.page.page.show', compact('page'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Page $page)
    {
        try {
            $templates = Page::PAGES_TEMPLATES;
            $pages = Page::where('parent_id', null)->get();
            return view('admin.page.page.edit', compact('page', 'templates', 'pages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'title' => 'required|string|min:6',
            'template' => 'required',
        ]);
        try {
            $page = $this->service->update($request, $page);
            return view('admin.page.page.show', compact('page'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function text(Request $request, Page $page)
    {
        try {
            $page = $this->service->setText($request, $page);
            return redirect()->route('admin.page.page.show', $page);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function draft(Page $page)
    {
        try {
            $page->draft();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function published(Page $page)
    {
        try {
            $page->published();
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Page $widget)
    {
        try {
            $this->service->destroy($widget);
            return redirect()->route('admin.page.page.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

}
