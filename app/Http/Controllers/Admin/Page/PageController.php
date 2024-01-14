<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Modules\Pages\Entity\Page;
use App\Modules\Pages\Service\PageService;
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
        $pages = Page::get();
        return view('admin.page.page.index', compact('pages'));
    }

    public function create()
    {
        $templates = Page::PAGES_TEMPLATES;
        return view('admin.page.page.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $page = $this->service->create($request);
        return view('admin.page.page.show', compact('page'));
    }

    public function show(Page $page)
    {
        return view('admin.page.page.show', compact('page'));
    }

    public function edit(Page $page)
    {
        $templates = Page::PAGES_TEMPLATES;
        return view('admin.page.page.edit', compact('page', 'templates'));
    }

    public function update(Request $request, Page $page)
    {
        $page = $this->service->update($request, $page);
        return view('admin.page.page.show', compact('page'));
    }

    public function destroy(Page $widget)
    {

        $this->service->destroy($widget);
        return redirect()->route('admin.page.page.index');
    }

}
