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

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

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

    public function index(Request $request): Response
    {
        $categories = $this->categories->forFilters();
        $groups = $this->groupRepository->get(order_by: 'name');
        $attributes = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Attribute/Index', [
            'attributes' => $attributes,
            'filters' => $filters,
            'categories' => $categories,
            'groups' => $groups,
            'types' => array_select(Attribute::ATTRIBUTES),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'group_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
        ]);
        try {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.product.attribute.show', $attribute)->with('success', 'Атрибут создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Attribute $attribute): Response
    {
        $categories = $this->categories->forFilters();
        $groups = $this->groupRepository->get(order_by: 'name');
        return Inertia::render('Product/Attribute/Show', [
            'attribute' => $this->repository->AttributeWithToArray($attribute),
            'categories' => $categories,
            'groups' => $groups,
            'types' => array_select(Attribute::ATTRIBUTES),
            'variant' => Attribute::TYPE_VARIANT,
        ]);
    }

    public function set_info(Request $request, Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->setInfo($request, $attribute);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->delete($attribute);
            return redirect()->back()->with('success', 'Атрибут удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //ГРУППЫ АТРИБУТОВ
    public function group_add(Request $request): RedirectResponse
    {
        try {
            $this->groupService->create($request);
            return redirect()->back()->with('success', 'Группа добавлена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function groups(Request $request): Response
    {
        $groups = $this->groupRepository->get(order_by: 'sort');
        return Inertia::render('Product/Attribute/Groups', [
            'groups' => $groups,
        ]);
    }

    public function group_up(AttributeGroup $group): RedirectResponse
    {
        try {
            $this->groupService->up($group);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function group_down(AttributeGroup $group): RedirectResponse
    {
        try {
            $this->groupService->down($group);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function group_rename(Request $request, AttributeGroup $group): RedirectResponse
    {
        try {
            $this->groupService->update($request, $group);
            return redirect()->back()->with('success', 'Группа переименована');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function group_destroy(AttributeGroup $group): RedirectResponse
    {
        try {
            $this->groupService->delete($group);
            return redirect()->back()->with('success', 'Группа удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*
    //Варианты
    public function variant_image(Request $request, AttributeVariant $variant)
    {
        $this->service->image_variant($variant, $request);
        return redirect()->back();
    }*/
}
