<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Group\CreateGroupUseCase;
use App\Modules\Catalog\Application\Actions\Group\IndexGroupQuery;
use App\Modules\Catalog\Application\Actions\Group\ListGroupUseCase;
use App\Modules\Catalog\Application\DTOs\Group\FilterGroupIndexData;
use App\Modules\Catalog\Application\DTOs\Group\GroupCreateData;
use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Catalog\Repository\GroupRepository;
use App\Modules\Catalog\Service\GroupService;
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
    private GroupService $service;
    private GroupRepository $repository;

    public function __construct(
        GroupService $service,
        GroupRepository $repository,
        private readonly ListGroupUseCase $listGroupUseCase,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly IndexGroupQuery $indexGroupQuery,
        private readonly CreateGroupUseCase $createGroupUseCase,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
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

    public function show(Group $group, Request $request): Response
    {
        $dto = new ContentBlockContainerData($group->id, ContainerType::GROUP);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);

        return Inertia::render('Catalog/Group/Show', [
            'group' => $this->repository->GroupWithToArray($group, $request),
            'blocks' => $blocks,
        ]);
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->service->delete($group);
        return redirect()->back()->with('success', 'Группа удалена');
    }

    public function add_product(Request $request, Group $group): RedirectResponse
    {
        $this->service->addProduct($group, (int)$request['product_id']);
        return redirect()->back()->with('success', 'Товар добавлен из группы');
    }

    public function add_products(Request $request, Group $group): RedirectResponse
    {
        $this->service->addProducts($group, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены из группы');
    }

    public function del_product(Request $request, Group $group): RedirectResponse
    {
        $this->service->del_product($request, $group);
        return redirect()->back()->with('success', 'Товар удален из группы');
    }

    public function set_info(Request $request, Group $group): RedirectResponse
    {
        $this->service->setInfo($group, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function search(Group $group, Request $request): JsonResponse
    {
        try {
            $products = $this->repository->search($group, $request);
            return \response()->json($products);
        } catch (\Throwable $e) {
            return \response()->json(['error' => $e->getMessage()]);
        }
    }

    public function list(): JsonResponse
    {
        $list = $this->listGroupUseCase->execute();
        return response()->json($list);
    }
}
