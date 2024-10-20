<?php
declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Repository\AttributeGroupRepository;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Service\AttributeGroupService;
use App\Modules\Product\Service\AttributeService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AttributeController extends Controller
{

    private AttributeService $service;
    private AttributeGroupService $groupService;
    private CategoryRepository $categories;
    private AttributeRepository $repository;
    private AttributeGroupRepository $groupRepository;


    public function __construct(
        AttributeService         $service,
        AttributeGroupService    $groupService,
        CategoryRepository       $categories,
        AttributeRepository      $repository,
        AttributeGroupRepository $groupRepository,
    )
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->groupService = $groupService;
        $this->categories = $categories;
        $this->repository = $repository;
        $this->groupRepository = $groupRepository;
    }

    public function index(Request $request)
    {
        $category_id = $request->get('category_id');
        $group_id = $request->get('group_id');

        $categories = $this->categories->withDepth();
        $groups = $this->groupRepository->get(order_by: 'name');
        $query = $this->repository->getIndex($category_id, $group_id);
        $prod_attributes = $this->pagination($query, $request, $pagination);

        return view('admin.product.attribute.index',
            compact('prod_attributes',
                'categories', 'groups', 'pagination', 'category_id', 'group_id'));
    }

    public function create()
    {
        $categories = $this->categories->withDepth();
        $groups = $this->groupRepository->get(order_by: 'name');
        return view('admin.product.attribute.create', compact('categories', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'group_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
        ]);
        $attribute = $this->service->create($request);
        return redirect()->route('admin.product.attribute.show', compact('attribute'));
    }

    public function show(Attribute $attribute)
    {
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function edit(Attribute $attribute)
    {
        $categories = $this->categories->withDepth();
        $groups = $this->groupRepository->get(order_by: 'name');
        return view('admin.product.attribute.edit', compact('attribute', 'categories', 'groups'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $this->service->update($request, $attribute);
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function destroy(Attribute $attribute)
    {
        $this->service->delete($attribute);
        return redirect()->route('admin.product.attribute.index');
    }

    //ГРУППЫ АТРИБУТОВ
    public function group_add(Request $request)
    {
        $this->groupService->create($request);
        return redirect()->back();
    }

    public function groups(Request $request)
    {
        $groups = $this->groupRepository->get(order_by: 'sort');
        return view('admin.product.attribute.groups', compact('groups'));
    }

    public function group_up(AttributeGroup $group)
    {
        $this->groupService->up($group);
        $groups = $this->groupRepository->get(order_by: 'sort');
        return redirect(route('admin.product.attribute.groups', compact('groups')));
    }

    public function group_down(AttributeGroup $group)
    {
        $this->groupService->down($group);
        $groups = $this->groupRepository->get(order_by: 'sort');
        return redirect(route('admin.product.attribute.groups', compact('groups')));
    }

    public function group_rename(Request $request, AttributeGroup $group)
    {
        $this->groupService->update($request, $group);
        return redirect()->back();
    }

    public function group_destroy(AttributeGroup $group)
    {
        $this->groupService->delete($group);
        return redirect()->back();
    }

    //Варианты
    public function variant_image(Request $request, AttributeVariant $variant)
    {
        $this->service->image_variant($variant, $request);
        return redirect()->back();
    }

}
