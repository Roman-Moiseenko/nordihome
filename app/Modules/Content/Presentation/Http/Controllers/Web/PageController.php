<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\Actions\Page\CreatePageUseCase;
use App\Modules\Content\Application\Actions\Page\IndexPageQuery;
use App\Modules\Content\Application\Actions\Page\ListParentPageQuery;
use App\Modules\Content\Application\Actions\Page\MovePageUseCase;
use App\Modules\Content\Application\Actions\Page\RemovePageUseCase;
use App\Modules\Content\Application\Actions\Page\TogglePageUseCase;
use App\Modules\Content\Application\Actions\Page\UpdatePageTextUseCase;
use App\Modules\Content\Application\Actions\Page\UpdatePageUseCase;
use App\Modules\Content\Application\Actions\Page\ViewPageQuery;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Application\DTOs\Page\PageCreateData;
use App\Modules\Content\Application\DTOs\Page\PageUpdateData;
use App\Modules\Content\Application\DTOs\Page\PageViewData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(
        private readonly TemplateRepository $templates,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly ListParentPageQuery $listParentPageQuery,
        private readonly IndexPageQuery $indexPageQuery,
        private readonly CreatePageUseCase $createPageUseCase,
        private readonly ViewPageQuery $viewPageQuery,
        private readonly UpdatePageUseCase $updatePageUseCase,
        private readonly UpdatePageTextUseCase $updatePageTextUseCase,
        private readonly TogglePageUseCase $togglePageUseCase,
        private readonly RemovePageUseCase $removePageUseCase,
        private readonly MovePageUseCase $movePageUseCase,
    ) {}

    public function index(Request $request): Response
    {
        $pages = $this->indexPageQuery->execute();
        $templates = $this->templates->getTemplates('page');

        return Inertia::render('Content/Page/Index', [
            'pages' => $pages,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = PageCreateData::validateAndCreate($request->all());
        $page = $this->createPageUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.content.page.show', $page->id)->with('success', 'Страница сохранена');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $page = $this->viewPageQuery->execute($id, $userPermission);
        $templates = $this->templates->getTemplates('page');

        $dto = new ContentBlockContainerData($page->id, ContainerType::PAGE);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Content/Page/Show', [
            'page' => Inertia::always(PageViewData::fromEntity($page)),
            'templates' => $templates,
            'tiny_api' => config('shop.tinymce'),
            'blocks' => $blocks,
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = PageUpdateData::validateAndCreate($request->all());
        $this->updatePageUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function updateText(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $text = $request->string('text')->trim()->value();
        $this->updatePageTextUseCase->execute($id, $text, $userPermission);

        return \response()->json(true);
    }

    public function toggle(int $id, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->togglePageUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', $message);
    }

    public function move(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $position = $request->integer('position', 0);
        $this->movePageUseCase->execute($id, $position, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removePageUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Страница удалена');
    }

    public function parentList()
    {
        $list = $this->listParentPageQuery->execute();
        return \response()->json($list);
    }
}
