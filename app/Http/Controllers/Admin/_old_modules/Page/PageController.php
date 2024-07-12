<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Page;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Service\PageService;
use Illuminate\Http\Request;
use function redirect;
use function view;

class PageController extends Controller
{
    private PageService $service;

    public function __construct(PageService $service)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $pages = Page::get();
            return view('admin.page.page.index', compact('pages'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            $templates = Page::PAGES_TEMPLATES;
            $pages = Page::where('parent_id', null)->get();
            return view('admin.page.page.create', compact('templates', 'pages'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'title' => 'required|string|min:6',
            'template' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $page = $this->service->create($request);
            return redirect()->route('admin.page.page.show', $page);
        });
    }

    public function show(Page $page)
    {
        return $this->try_catch_admin(function () use($page) {
            return view('admin.page.page.show', compact('page'));
        });
    }

    public function edit(Page $page)
    {
        return $this->try_catch_admin(function () use($page) {
            $templates = Page::PAGES_TEMPLATES;
            $pages = Page::where('parent_id', null)->get();
            return view('admin.page.page.edit', compact('page', 'templates', 'pages'));
        });
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'title' => 'required|string|min:6',
            'template' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request, $page) {
            $page = $this->service->update($request, $page);
            return redirect()->route('admin.page.page.show', $page);
        });
    }

    public function text(Request $request, Page $page)
    {
        return $this->try_catch_admin(function () use($request, $page) {
            $page = $this->service->setText($request, $page);
            return redirect()->route('admin.page.page.show', $page);
        });
    }

    public function draft(Page $page)
    {
        return $this->try_catch_admin(function () use($page) {
            $page->draft();
            return redirect()->back();
        });
    }

    public function published(Page $page)
    {
        return $this->try_catch_admin(function () use($page) {
            $page->published();
            return redirect()->back();
        });
    }

    public function destroy(Page $page)
    {
        return $this->try_catch_admin(function () use($page) {
            $this->service->destroy($page);
            return redirect()->route('admin.page.page.index');
        });
    }

}
