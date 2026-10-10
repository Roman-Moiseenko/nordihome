<?php
declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\Actions\Page\IndexPageQuery;
use App\Modules\Content\Application\Actions\Page\ListParentPageQuery;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Infrastructure\Models\Page;
use App\Modules\Content\Repository\PageRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\PageService;
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
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly ListParentPageQuery $listParentPageQuery,
        private readonly IndexPageQuery $indexPageQuery,
    )
    {
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $pages = $this->indexPageQuery->execute();
        $templates = $this->templates->getTemplates('page');

        return Inertia::render('Content/Page/Index', [
            'pages' => $pages,
            'templates' => $templates,
        ]);

    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|min:3',
        ]);
        $page = $this->service->create($request);
        return redirect()->route('admin.content.page.show', $page)->with('success', 'Страница сохранена');
    }

    public function show(Page $page): Response
    {
        $templates = $this->templates->getTemplates('page');

        $dto = new ContentBlockContainerData($page->id, ContainerType::PAGE);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Content/Page/Show', [
            'page' => $this->repository->PageWithToArray($page),
            'templates' => $templates,
            'tiny_api' => config('shop.tinymce'),
            'blocks' => $blocks,
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
        $message = $this->service->toggle($page);
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


    public function parentList()
    {
        $list = $this->listParentPageQuery->execute();
        return \response()->json($list);
    }

}
