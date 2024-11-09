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
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $distributors = Distributor::orderBy('name')->getModels();
        $stack_count = SupplyStack::where('supply_id', null)->count();
        $supplies = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();
        /*return view('admin.accounting.supply.index', compact('supplies', 'filters', 'distributors', 'stack_count', 'staffs'));*/
        return Inertia::render('Accounting/Supply/Index', [
            'supplies' => $supplies,
            'filters' => $filters,
            'distributors' => $distributors,
            'stack_count' => $stack_count,
            'staffs' => $staffs
        ]);
    }

    public function stack(Request $request)
    {
        $brands = Brand::orderBy('name')->getModels();
        $stacks = $this->repository->getStacks($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();
        //return view('admin.accounting.supply.stack', compact('stacks', 'distributors'));

        return Inertia::render('Accounting/Supply/Stack', [
            'stacks' => $stacks,
            'filters' => $filters,
            'brands' => $brands,
            'staffs' => $staffs
        ]);
    }

    public function create(Request $request)
    {
        $distributor = Distributor::find($request->integer('distributor'));
        $stacks = $this->stacks->getByDistributor($distributor);
        if (!empty($stacks)) { //Если стек не пуст, то показываем
            //return view('admin.accounting.supply.create', compact('stacks', 'distributor'));
            return Inertia::render('Accounting/Supply/Create', [
                'stacks' => $stacks,
                'distributor' => $distributor,
            ]);
        } else { //Иначе создаем пустой заказ
            $supply = $this->service->create_empty($distributor);
            return redirect()->route('admin.accounting.supply.show', $supply);
        }
    }

    //TODO Vue3
    public function show(SupplyDocument $supply)
    {
        //return view('admin.accounting.supply.show', compact('supply'));
        return Inertia::render('Accounting/Supply/Show', [
            'supply' => $this->repository->SupplyWithToArray($supply),
        ]);
    }

    public function store(Request $request)
    {
        $supply = $this->service->create($request->integer('distributor'), $request['stacks']);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function destroy(SupplyDocument $supply)
    {
        $this->service->destroy($supply);
        flash('Заказ удален', 'success');
        return redirect()->back()->with('success', 'Заказ удален успешно');
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
        $this->service->completed($supply);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function add_stack(Request $request, OrderItem $item): RedirectResponse
    {
        $request->validate([
            'storage' => 'required|numeric|min:0|not_in:0',
        ]);
        try {
            $stack = $this->service->add_stack($item, $request->integer('storage'));
            if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
            return redirect()->back()->with('success', 'Товар ' . $stack->product->name . ' помещен в стек заказа');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function del_stack(SupplyStack $stack): RedirectResponse
    {
        $this->service->del_stack($stack);
        return redirect()->back();
    }

    public function add_product(SupplyDocument $supply, Request $request): RedirectResponse
    {
        try {
            $this->service->add_product($supply, $request->integer('product_id'), $request->integer('quantity'));
            return redirect()->back()->with('success', 'Товары добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(SupplyDocument $supply, Request $request): RedirectResponse
    {
        try {
            $this->service->add_products($supply, $request->input('products'));
            return redirect()->back()->with('success', 'Товары добавлены');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(SupplyProduct $product)
    {
        try {
            $this->service->del_product($product);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function set_product(SupplyProduct $product, Request $request)
    {
        try {
            $this->service->set_product($product, $request->integer('quantity'), $request->float('cost'));
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(SupplyDocument $supply, Request $request)
    {
        try {
            $this->service->set_info($supply, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
