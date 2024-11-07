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
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Repository\SupplyRepository;
use App\Modules\Accounting\Service\SupplyService;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    private SupplyService $service;
    private ProductRepository $products;
    private StackRepository $stacks;
    private SupplyRepository $repository;
    private StaffRepository $staffs;

    public function __construct(
        SupplyService $service,
        ProductRepository $products,
        StackRepository $stacks,
        SupplyRepository $repository,
        StaffRepository $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
        $this->stacks = $stacks;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request)
    {
        $distributors = Distributor::orderBy('name')->get();
        $stack_count = SupplyStack::where('supply_id', null)->count();
        $supplies = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();

        return view('admin.accounting.supply.index',
            compact('supplies', 'filters', 'distributors', 'stack_count', 'staffs'));
    }

    public function create(Request $request)
    {
        $distributor = Distributor::find($request->integer('distributor'));
        $stacks = $this->stacks->getByDistributor($distributor);
        if (!empty($stacks)) { //Если стек не пуст, то показываем
            return view('admin.accounting.supply.create', compact('stacks', 'distributor'));
        } else { //Иначе создаем пустой заказ
            $supply = $this->service->create_empty($distributor);
            return redirect()->route('admin.accounting.supply.show', $supply);
        }
    }

    public function store(Request $request)
    {
        $supply = $this->service->create($request->integer('distributor'), $request['stack']);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function show(SupplyDocument $supply)
    {
        return view('admin.accounting.supply.show', compact('supply'));
    }

    public function destroy(SupplyDocument $supply)
    {
        $this->service->destroy($supply);
        return redirect()->back();
    }

    public function copy(SupplyDocument $supply): RedirectResponse
    {
        $supply = $this->service->copy($supply);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function sent(SupplyDocument $supply): RedirectResponse
    {
        $this->service->sent($supply);
        return redirect()->back();
    }

    public function completed(SupplyDocument $supply): RedirectResponse
    {
        $arrival = $this->service->completed($supply);
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function stack(Request $request)
    {
        $distributors = Distributor::orderBy('name')->get();
        $stacks = SupplyStack::where('supply_id', null)->get();
        return view('admin.accounting.supply.stack', compact('stacks', 'distributors'));
    }

    public function add_stack(Request $request, OrderItem $item): RedirectResponse
    {
        $request->validate([
            'storage' => 'required|numeric|min:0|not_in:0',
        ]);
        $stack = $this->service->add_stack($item, $request->integer('storage'));
        if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
        return redirect()->back();
    }

    public function del_stack(SupplyStack $stack): RedirectResponse
    {
        $this->service->del_stack($stack);
        return redirect()->back();
    }

    public function add_product(SupplyDocument $supply, Request $request): RedirectResponse
    {
        $this->service->add_product($supply, $request->integer('product_id'), $request->integer('quantity'));
        return redirect()->back();
    }

    public function add_products(SupplyDocument $supply, Request $request): RedirectResponse
    {
        $this->service->add_products($supply, $request['products']);
        return redirect()->back();
    }

    public function del_product(SupplyProduct $product)
    {
        $this->service->del_product($product);
        return redirect()->back();
    }

    //AJAX
    public function set_product(SupplyProduct $product, Request $request)
    {
        $this->service->set_product($product, $request->integer('quantity'), $request->float('cost'));
        return response()->json(true);
    }
}
