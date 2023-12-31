<?php

namespace App\Http\Controllers\Admin\Product;

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

    private mixed $pagination;
    private ModificationService $service;
    private ProductRepository $products;
    private ModificationRepository $repository;

    public function __construct(ModificationService $service, ProductRepository $products, ModificationRepository $repository)
    {
        $this->pagination = Config::get('shop-config.p-list');
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $query = Modification::orderBy('name');
        if (!empty($name = $request['name'])) {
            $query = $query->where('name', 'LIKE', "%{$name}%");
        }
        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $modifications = $query->paginate($pagination);
            $modifications->appends(['p' => $pagination]);
        } else {
            $modifications = $query->paginate($this->pagination);
        }

        return view('admin.product.modification.index', compact('modifications', 'pagination'));
    }

    public function create(Request $request)
    {
        //По ссылке из товара
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
        try {
            $this->service->delete($modification);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.product.modification.index');
    }

//AJAX
    public function search(Request $request)
    {
        //$request - содержит параметр action = index, create и show //
        $result = [];
        $products = [];
        try {
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
                if (empty($product->modification())) {
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

        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
        }
        return \response()->json($result);
    }

    public function add_product(Request $request, Modification $modification)
    {
        try {
            $this->service->add_product($request, $modification);
            return \response()->json(true);
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');
            return \response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    public function del_product(Request $request, Modification $modification)
    {
        try {
            $this->service->del_product($request, $modification);
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');
        }
        return redirect()->route('admin.product.modification.show', compact('modification'));

    }

}
