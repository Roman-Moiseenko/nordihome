<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Service\AttributeGroupService;
use App\Modules\Product\Service\AttributeService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class AttributeController extends Controller
{

    private mixed $pagination;
    private AttributeService $service;
    private AttributeGroupService $groupService;


    public function __construct(AttributeService $service, AttributeGroupService $groupService)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->pagination = Config::get('shop-config.p-list');
        $this->service = $service;
        $this->groupService = $groupService;
    }

    public function index(Request $request)
    {
        //TODO Перенести все в Repository!!!
        $query = Attribute::orderBy('name');
        $categories = Category::defaultOrder()->withDepth()->get();
        $groups = AttributeGroup::orderBy('name')->get();

        if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('id', '=', $category_id);
            });
        }

        //if ($category_id == 0) unset($category_id);
        if (!empty($group_id = $request->get('group_id'))) {
            $query->where('group_id', $group_id);
        }

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $prod_attributes = $query->paginate($pagination);
            $prod_attributes->appends(['p' => $pagination]);
        } else {
            $prod_attributes = $query->paginate($this->pagination);
        }


        return view('admin.product.attribute.index',
            compact('prod_attributes',
                'categories', 'groups', 'pagination', 'category_id', 'group_id'));
    }


    public function create()
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $groups = AttributeGroup::orderBy('name')->get();
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
        try {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.product.attribute.show', compact('attribute'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }

    }

    public function show(Attribute $attribute)
    {
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function edit(Attribute $attribute)
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $groups = AttributeGroup::orderBy('name')->get();
        return view('admin.product.attribute.edit', compact('attribute', 'categories', 'groups'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $attribute = $this->service->update($request, $attribute);
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function destroy(Attribute $attribute)
    {
        try {
            $this->service->delete($attribute);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect('admin/product/attribute');
    }


    //ГРУППЫ АТРИБУТОВ

    public function group_add(Request $request)
    {
        $this->groupService->create($request);
        return back();
    }

    public function groups(Request $request)
    {
        $groups = AttributeGroup::orderBy('sort')->get();
        return view('admin.product.attribute.groups', compact('groups'));
    }

    public function group_up(AttributeGroup $group)
    {
        $this->groupService->up($group);
        $groups = AttributeGroup::orderBy('sort')->get();
        return redirect(route('admin.product.attribute.groups', compact('groups')));
    }

    public function group_down(AttributeGroup $group)
    {
        $this->groupService->down($group);
        $groups = AttributeGroup::orderBy('sort')->get();
        return redirect(route('admin.product.attribute.groups', compact('groups')));
    }

    public function group_rename(Request $request, AttributeGroup $group)
    {
        $this->groupService->update($request, $group);
        return back();
    }

    public function group_destroy(AttributeGroup $group)
    {
        $this->groupService->delete($group);
        return back();
    }

    //Варианты

    public function variant_image(Request $request, AttributeVariant $variant)
    {
        $this->service->image_variant($variant, $request);
        return back();
    }

}
