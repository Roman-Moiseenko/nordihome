<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Service\SupplyService;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    private SupplyService $service;
    private ProductRepository $products;

    public function __construct(SupplyService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $query = SupplyDocument::orderByDesc('created_at');
            $distributors = Distributor::orderBy('name')->get();
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';

            if ($completed == 'created') $query->where('status', SupplyDocument::CREATED);
            if ($completed == 'sent') $query->where('status', SupplyDocument::SENT);
            if ($completed == 'completed') $query->where('status', SupplyDocument::COMPLETED);
            if (!empty($distributor_id = $request->get('distributor_id'))) {
                $query->where('distributor_id', $distributor_id);
            }
            if (!empty($storage_id = $request->get('storage_id'))) {
                $query->where('storage_id', $storage_id);
            }

            $supplies = $this->pagination($query, $request, $pagination);
            $stack_count = SupplyStack::where('supply_id', null)->count();

            return view('admin.accounting.supply.index',
                compact('supplies', 'pagination', 'completed', 'storages', 'distributors', 'storage_id', 'distributor_id', 'stack_count'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            /** @var Distributor $distributor */
            $distributor = Distributor::find((int)$request['distributor']);
            /** @var SupplyStack[] $stacks */
            $stacks = SupplyStack::where('supply_id', null)->getModels();
            foreach ($stacks as $i => $stack) {
                if (DistributorProduct::where('product_id', $stack->product->id)->get())
                if (!$distributor->isProduct($stack->product))
                    unset($stacks[$i]);
            }
            if (!empty($stacks)) { //Если стек не пуст, то показываем
                return view('admin.accounting.supply.create', compact('stacks', 'distributor'));
            } else { //Иначе создаем пустой заказ
                $supply = $this->service->create_empty($distributor);
                return redirect()->route('admin.accounting.supply.show', $supply);
            }
        });
    }
    public function store(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $supply = $this->service->create((int)$request['distributor'], $request['stack']);
            return redirect()->route('admin.accounting.supply.show', $supply);
        });
    }

    public function show(SupplyDocument $supply)
    {
        return $this->try_catch_admin(function () use ($supply) {
            return view('admin.accounting.supply.show', compact('supply'));
        });
    }

    public function sent(SupplyDocument $supply)
    {
        return $this->try_catch_admin(function () use ($supply) {
            $this->service->sent($supply);
            return redirect()->back();
        });
    }

    public function completed(SupplyDocument $supply)
    {
        return $this->try_catch_admin(function () use ($supply) {
            $this->service->completed($supply);
            return redirect()->back();
        });
    }

    public function stack(Request $request)
    {
        return $this->try_catch_admin(function () use ($request) {
            $distributors = Distributor::orderBy('name')->get();
            $stacks = SupplyStack::where('supply_id', null)->get();
            return view('admin.accounting.supply.stack', compact('stacks' ,'distributors'));
        });
    }

    public function add_stack(Request $request, OrderItem $item)
    {
        $request->validate([
            'storage' => 'required|numeric|min:0|not_in:0',
        ]);
        return $this->try_catch_admin(function () use ($request, $item) {
            $stack = $this->service->add_stack($item, (int)$request['storage']);
            if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
            return redirect()->back();
        });
    }

    public function del_stack(SupplyStack $stack)
    {
        return $this->try_catch_admin(function () use ($stack) {
            $this->service->del_stack($stack);
            return redirect()->back();
        });
    }

    public function add_product(SupplyDocument $supply, Request $request)
    {
        return $this->try_catch_admin(function () use ($supply, $request) {
            $this->service->add_product($supply, (int)$request['product_id'], (int)$request['quantity']);
            return redirect()->back();
        });
    }

    public function add_products(SupplyDocument $supply, Request $request)
    {
        return $this->try_catch_admin(function () use ($supply, $request) {
            $this->service->add_products($supply, $request['products']);
            return redirect()->back();
        });
    }

    public function del_product(SupplyProduct $product)
    {
        return $this->try_catch_admin(function () use ($product) {
            $this->service->del_product($product);
            return redirect()->back();
        });
    }

    //AJAX
    public function set_product(SupplyProduct $product, Request $request)
    {
        return $this->try_catch_ajax_admin(function () use ($product, $request) {
            $this->service->set_product($product, (int)$request['quantity']);
            return response()->json(true);
        });
    }

    public function search(Request $request, SupplyDocument $supply)
    {
        return $this->try_catch_ajax_admin(function () use($request, $supply) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$supply->isProduct($product)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }
}
