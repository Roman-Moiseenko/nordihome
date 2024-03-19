<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Service\AttributeGroupService;
use App\Modules\Product\Service\AttributeService;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AttributeController extends Controller
{

    private AttributeService $service;
    private AttributeGroupService $groupService;


    public function __construct(
        AttributeService $service,
        AttributeGroupService $groupService)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->groupService = $groupService;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {

            //TODO Перенести все в Repository!!!
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();

            $query = Attribute::orderBy('name');
            if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
                $query->whereHas('categories', function ($q) use ($category_id) {
                    $q->where('id', '=', $category_id);
                });
            }
            if (!empty($group_id = $request->get('group_id'))) {
                $query->where('group_id', $group_id);
            }

            $prod_attributes = $this->pagination($query, $request, $pagination);

            return view('admin.product.attribute.index',
                compact('prod_attributes',
                    'categories', 'groups', 'pagination', 'category_id', 'group_id'));
        });
    }


    public function create()
    {
        return $this->try_catch_admin(function () {
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();
            return view('admin.product.attribute.create', compact('categories', 'groups'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'group_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.product.attribute.show', compact('attribute'));
        });
    }

    public function show(Attribute $attribute)
    {
        return $this->try_catch_admin(function () use($attribute) {
            return view('admin.product.attribute.show', compact('attribute'));
        });
    }

    public function edit(Attribute $attribute)
    {
        return $this->try_catch_admin(function () use($attribute) {
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();
            return view('admin.product.attribute.edit', compact('attribute', 'categories', 'groups'));
        });
    }

    public function update(Request $request, Attribute $attribute)
    {
        return $this->try_catch_admin(function () use($request, $attribute) {
            $attribute = $this->service->update($request, $attribute);
            return view('admin.product.attribute.show', compact('attribute'));
        });
    }

    public function destroy(Attribute $attribute)
    {
        return $this->try_catch_admin(function () use($attribute) {
            $this->service->delete($attribute);
            return redirect()->route('admin.product.attribute.index');
        });
    }

    //ГРУППЫ АТРИБУТОВ
    public function group_add(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $this->groupService->create($request);
            return redirect()->back();
        });
    }

    public function groups(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $groups = AttributeGroup::orderBy('sort')->get();
            return view('admin.product.attribute.groups', compact('groups'));
        });
    }

    public function group_up(AttributeGroup $group)
    {
        return $this->try_catch_admin(function () use($group) {
            $this->groupService->up($group);
            $groups = AttributeGroup::orderBy('sort')->get();
            return redirect(route('admin.product.attribute.groups', compact('groups')));
        });
    }

    public function group_down(AttributeGroup $group)
    {
        return $this->try_catch_admin(function () use($group) {
            $this->groupService->down($group);
            $groups = AttributeGroup::orderBy('sort')->get();
            return redirect(route('admin.product.attribute.groups', compact('groups')));
        });
    }

    public function group_rename(Request $request, AttributeGroup $group)
    {
        return $this->try_catch_admin(function () use($request, $group) {
            $this->groupService->update($request, $group);
            return redirect()->back();
        });
    }

    public function group_destroy(AttributeGroup $group)
    {
        return $this->try_catch_admin(function () use($group) {
            $this->groupService->delete($group);
            return redirect()->back();
        });
    }

    //Варианты
    public function variant_image(Request $request, AttributeVariant $variant)
    {
        return $this->try_catch_admin(function () use($request, $variant) {
            $this->service->image_variant($variant, $request);
            return redirect()->back();
        });
    }

}
