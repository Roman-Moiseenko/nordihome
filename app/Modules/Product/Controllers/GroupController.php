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
        //$this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $query = Group::orderBy('name');
        if (!empty($name = $request['search'])) {
            $query = $query->where('name', 'LIKE', "%{$name}%");
        }
        $groups = $this->pagination($query, $request, $pagination);
        return view('admin.product.group.index', compact('groups', 'pagination'));
    }

    public function create()
    {
        return view('admin.product.group.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $group = $this->service->create($request);
        return redirect()->route('admin.product.group.show', compact('group'));
    }

    public function show(Group $group)
    {
        return view('admin.product.group.show', compact('group'));
    }

    public function edit(Group $group)
    {
        return view('admin.product.group.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $group = $this->service->update($request, $group);
        return redirect()->route('admin.product.group.show', compact('group'));
    }

    public function destroy(Group $group)
    {
        $this->service->delete($group);
        return redirect()->route('admin.product.group.index');
    }

    public function add_product(Request $request, Group $group)
    {
        $this->service->add_product($group, (int)$request['product_id']);
        return redirect()->route('admin.product.group.show', compact('group'));
    }

    public function add_products(Request $request, Group $group)
    {

        $this->service->add_products($group, $request['products']);
        return redirect()->route('admin.product.group.show', compact('group'));
    }


    public function del_product(Request $request, Group $group)
    {
        $this->service->del_product($request, $group);
        return redirect()->route('admin.product.group.show', compact('group'));
    }

}
