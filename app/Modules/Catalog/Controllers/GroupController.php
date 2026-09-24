<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Group\ListGroupUseCase;
use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Catalog\Repository\GroupRepository;
use App\Modules\Catalog\Service\GroupService;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
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
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $groups = $this->repository->getIndex($request, $filters);
        return Inertia::render('Catalog/Group/Index', [
            'groups' => $groups,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $group = $this->service->create($request);
        return redirect()->route('admin.catalog.group.show', $group)->with('success', 'Группа создана');
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
