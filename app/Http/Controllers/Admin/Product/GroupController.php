<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Service\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GroupController extends Controller
{
    private mixed $pagination;
    private GroupService $service;

    public function __construct(GroupService $service)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        $query = Group::orderBy('name');
        if (!empty($name = $request['name'])) {
            $query = $query->where('name','LIKE',"%{$name}%");
        }
        $groups = $query->paginate($this->pagination);
        return view('admin.product.group.index', compact('groups'));
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
        try {
            $this->service->delete($group);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.product.group.index');
    }
}
