<?php

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GroupController extends Controller
{
    private mixed $pagination;
    private GroupService $service;
    private ProductRepository $products;

    public function __construct(GroupService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
        $this->products = $products;
    }

    public function index(Request $request)
    {
        try {
            $query = Group::orderBy('name');
            if (!empty($name = $request['search'])) {
                $query = $query->where('name', 'LIKE', "%{$name}%");
            }
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $groups = $query->paginate($pagination);
                $groups->appends(['p' => $pagination]);
            } else {
                $groups = $query->paginate($this->pagination);
            }
            return view('admin.product.group.index', compact('groups', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();

    }

    public function create()
    {
        try {
            return view('admin.product.group.create');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        try {
            $group = $this->service->create($request);
            return redirect()->route('admin.product.group.show', compact('group'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Group $group)
    {
        try {
            return view('admin.product.group.show', compact('group'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Group $group)
    {
        try {
            return view('admin.product.group.edit', compact('group'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        try {
            $group = $this->service->update($request, $group);
            return redirect()->route('admin.product.group.show', compact('group'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Group $group)
    {
        try {
            $this->service->delete($group);
            return redirect()->route('admin.product.group.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();

    }

    public function add_product(Request $request, Group $group)
    {
        try {
            $this->service->add_product($request, $group);
            return redirect()->route('admin.product.group.show', compact('group'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function del_product(Request $request, Group $group)
    {
        try {
            $this->service->del_product($request, $group);
            return redirect()->route('admin.product.group.show', compact('group'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function search(Request $request, Group $group)
    {
        $result = [];
        try {
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$group->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }
        return \response()->json($result);
    }
}
