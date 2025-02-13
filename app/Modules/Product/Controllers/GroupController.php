<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\GroupRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    private GroupService $service;
    private ProductRepository $products;
    private GroupRepository $repository;

    public function __construct(GroupService $service, ProductRepository $products, GroupRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $groups = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Group/Index', [
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
        return redirect()->route('admin.product.group.show', $group)->with('success', 'Группа создана');
    }

    public function show(Group $group, Request $request): Response
    {
        return Inertia::render('Product/Group/Show', [
            'group' => $this->repository->GroupWithToArray($group, $request),
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
}
