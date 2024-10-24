<?php

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ModificationRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\ModificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ModificationController extends Controller
{
    private ModificationService $service;
    private ProductRepository $products;
    private ModificationRepository $repository;

    public function __construct(ModificationService $service, ProductRepository $products, ModificationRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $query = $this->repository->getIndex($request->string('name')->trim()->value());
        $modifications = $this->pagination($query, $request, $pagination);
        return view('admin.product.modification.index', compact('modifications', 'pagination'));
    }

    public function create(Request $request)
    {
        $product_id = $request['product_id'] ?? null;
        return view('admin.product.modification.create', compact('product_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'required',
            'attribute_id' => 'required|array',
        ]);
        $modification = $this->service->create($request);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }

    public function show(Modification $modification)
    {
        return view('admin.product.modification.show', compact('modification'));
    }

    public function edit(Modification $modification)
    {
        return view('admin.product.modification.edit', compact('modification'));
    }


    public function update(Request $request, Modification $modification)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'integer|required',
        ]);
        $modification = $this->service->update($request, $modification);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }

    public function destroy(Modification $modification)
    {
        $this->service->delete($modification);
        return redirect()->route('admin.product.modification.index');
    }

    public function del_product(Request $request, Modification $modification)
    {
        $this->service->del_product($request, $modification);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }

//AJAX
//TODO Переделать
    public function search(Request $request)
    {
        $result = [];
        $products = [];
        if (empty($request['action'])) {
            $products = $this->products->search($request['search'], 100000);
        } else {
            if ($request['action'] == 'index') {
                $product_in = $this->repository->getAllIdsArray();
                if (!empty($product_in)) $products = $this->products->search($request['search'], 100000, $product_in);
            }
            if ($request['action'] == 'create') {
                $product_in = $this->repository->getAllIdsArray();
                $products = $this->products->search($request['search'], 100000, $product_in, false);
            }
            if ($request['action'] == 'show') {
                $product_in = $this->repository->getAssignmentIdsArray();
                $products = $this->products->search($request['search'], 100000, $product_in, false);
            }
        }

        //TODO Сделать фильтрацию по товарам которые есть в любой модификации (получить все id из ModiRepository и перебрать и проверить in_array($product->id, $array_mod_ids)
        /** @var Product $product */
        foreach ($products as $product) {
            if (is_null($product->modification)) {
                $result[] = $this->products->toArrayForSearch($product);
            } else {
                if ($request['action'] == 'index') {
                    $other = route('admin.product.modification.show', $product->modification);
                } else {
                    $other = $product->modification->id;
                }
                $result[] = array_merge($this->products->toArrayForSearch($product), ['other' => $other]);
            }
        }
        return \response()->json($result);
    }

    public function add_product(Request $request, Modification $modification)
    {
        $this->service->add_product($request, $modification);
        return \response()->json(true);
    }


}
