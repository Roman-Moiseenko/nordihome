<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Group\CreateGroupUseCase;
use App\Modules\Catalog\Application\Actions\Group\IndexGroupQuery;
use App\Modules\Catalog\Application\Actions\Group\ListGroupQuery;
use App\Modules\Catalog\Application\Actions\Group\RemoveGroupUseCase;
use App\Modules\Catalog\Application\Actions\Group\UpdateGroupUseCase;
use App\Modules\Catalog\Application\Actions\Group\ViewGroupQuery;
use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Application\DTOs\Group\GroupCreateData;
use App\Modules\Catalog\Application\DTOs\Group\GroupUpdateData;
use App\Modules\Catalog\Application\DTOs\Group\GroupViewData;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{

    public function __construct(
        private readonly ListGroupQuery                     $listGroupUseCase,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly IndexGroupQuery                    $indexGroupQuery,
        private readonly CreateGroupUseCase                 $createGroupUseCase,
        private readonly ViewGroupQuery                     $viewGroupQuery,
        private readonly UpdateGroupUseCase                 $updateGroupUseCase,
        private readonly RemoveGroupUseCase                 $removeGroupUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission)
    {
        $filterDto = FilterGroupIndexData::validateAndCreate($request->all());
        $groups = $this->indexGroupQuery->execute($filterDto, $userPermission);

        return Inertia::render('Catalog/Group/Index', [
            'groups' => $groups,
            'filters' => $filterDto,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = GroupCreateData::validateAndCreate($request->all());
        $group = $this->createGroupUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.catalog.group.show', $group->id)->with('success', 'Группа создана');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $groupEntity = $this->viewGroupQuery->execute($id, $userPermission);

        $dto = new ContentBlockContainerData($id, ContainerType::GROUP);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Catalog/Group/Show', [
            'group' => GroupViewData::fromEntity($groupEntity),
            'blocks' => $blocks,
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = GroupUpdateData::validateAndCreate($request->all());
        $group = $this->updateGroupUseCase->execute($id, $dto, $userPermission);

        return redirect()->route('admin.catalog.group.show', $group->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeGroupUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Группа удалена');
    }

    public function list(): JsonResponse
    {
        $list = $this->listGroupUseCase->execute();
        return response()->json($list);
    }
}
