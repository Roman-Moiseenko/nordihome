<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Page;
use App\Modules\Page\Repository\PageRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    private PageService $service;
    private TemplateRepository $templates;
    private PageRepository $repository;

    public function __construct(
        PageService        $service,
        TemplateRepository $templates,
        PageRepository     $repository,
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $parent_pages = Page::where('parent_id', null)->get();

        $pages = $this->repository->getIndex($request);
        $templates = $this->templates->getTemplates('page');

        return Inertia::render('Page/Page/Index', [
            'pages' => $pages,
            'templates' => $templates,
            'parent_pages' => $parent_pages,
        ]);

    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3',
            //'title' => 'required|string|min:6',
            'template' => 'required',
        ]);
        $page = $this->service->create($request);
        return redirect()->route('admin.page.page.show', $page)->with('success', 'Страница сохранена');
    }

    public function show(Page $page): Response
    {
        $templates = $this->templates->getTemplates('page');
        $parent_pages = Page::where('parent_id', null)->get();

        return Inertia::render('Page/Page/Show', [
            'page' => $this->repository->PageWithToArray($page),
            'templates' => $templates,
            'parent_pages' => $parent_pages,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function set_info(Page $page, Request $request): RedirectResponse
    {
        $this->service->setInfo($page, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_text(Page $page, Request $request): JsonResponse
    {
        $this->service->setText($page, $request->string('text')->trim()->value());
        return \response()->json(true);
    }

    public function toggle(Page $page): RedirectResponse
    {
        if ($page->published) {
            $message = 'Страница убрана из публикации';
            $page->draft();
        } else {
            $message = 'Страница опубликована';
            $page->published();
        }
        return redirect()->back()->with('success', $message);
    }

    public function up(Page $page)
    {
        $this->service->up($page);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(Page $page)
    {
        $this->service->down($page);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->service->destroy($page);
        return redirect()->back()->with('success', 'Страница удалена');
    }

}
