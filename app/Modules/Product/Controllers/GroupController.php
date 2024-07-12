<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GroupController extends Controller
{
    private GroupService $service;
    private ProductRepository $products;

    public function __construct(GroupService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Group::orderBy('name');
            if (!empty($name = $request['search'])) {
                $query = $query->where('name', 'LIKE', "%{$name}%");
            }
            $groups = $this->pagination($query, $request, $pagination);
            return view('admin.product.group.index', compact('groups', 'pagination'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            return view('admin.product.group.create');
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        return $this->try_catch_admin(function () use($request) {
            $group = $this->service->create($request);
            return redirect()->route('admin.product.group.show', compact('group'));
        });
    }

    public function show(Group $group)
    {
        return $this->try_catch_admin(function () use($group) {
            return view('admin.product.group.show', compact('group'));
        });
    }

    public function edit(Group $group)
    {
        return $this->try_catch_admin(function () use($group) {
            return view('admin.product.group.edit', compact('group'));
        });
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        return $this->try_catch_admin(function () use($request, $group) {
            $group = $this->service->update($request, $group);
            return redirect()->route('admin.product.group.show', compact('group'));
        });
    }

    public function destroy(Group $group)
    {
        return $this->try_catch_admin(function () use($group) {
            $this->service->delete($group);
            return redirect()->route('admin.product.group.index');
        });
    }

    public function add_product(Request $request, Group $group)
    {
        return $this->try_catch_admin(function () use($request, $group) {
            $this->service->add_product($group, (int)$request['product_id']);
            return redirect()->route('admin.product.group.show', compact('group'));
        });
    }

    public function add_products(Request $request, Group $group)
    {
        return $this->try_catch_admin(function () use($request, $group) {

            $this->service->add_products($group, $request['products']);
            return redirect()->route('admin.product.group.show', compact('group'));
        });
    }


    public function del_product(Request $request, Group $group)
    {
        return $this->try_catch_admin(function () use($request, $group) {
            $this->service->del_product($request, $group);
            return redirect()->route('admin.product.group.show', compact('group'));
        });
    }

    //AJAX
    public function search(Request $request, Group $group)
    {
        return $this->try_catch_ajax_admin(function () use($request, $group) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$group->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }
}
