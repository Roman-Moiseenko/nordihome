<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class AttributeController extends Controller
{

    private mixed $pagination;


    public function __construct()
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
 /*       $pagination = $request['p'] ?? $this->pagination;
        $brands = $this->repository->getIndex($pagination);
        if (isset($request['p'])) {
            $brands->appends(['p' => $pagination]);
        }*/
        $query = Attribute::orderBy('name');
        $categories = Category::defaultOrder()->withDepth()->get();
        $groups = AttributeGroup::orderBy('name')->get();

       ;
        if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
            $query->where('category_id', $category_id);
        }
        //if ($category_id == 0) unset($category_id);
        if (!empty($group_id = $request->get('group_id'))) {
            $query->where('group_id', $group_id);
        }

        //$attributes = [];

        //$pagination = null;
        //$categories = [];
        //$groups = [];
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
/*
    public function create()
    {
        return view('admin.product.attribute.create');
    }

    public function store(Request $request)
    {

        return redirect()->route('admin.product.attribute.show', compact(''));
    }

    public function show(Attribute $attribute)
    {
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.product.attribute.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        //$attribute = $this->service->update($request, $brand);
        return view('admin.product.attribute.show', compact('attribute'));
    }

    public function destroy(Attribute $attribute)
    {
        try {
            //$this->service->delete($brand);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect('admin/product/attribute');
    }*/
}
