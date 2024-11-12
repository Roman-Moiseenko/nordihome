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
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

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
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work']);
        $this->service = $service;
        $this->products = $products;
        $this->stacks = $stacks;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request): Response
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

    public function stack(Request $request): Response
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

    public function create(Request $request): Response|RedirectResponse
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
            $supply = $this->service->createEmpty($distributor);
            return redirect()->route('admin.accounting.supply.show', $supply);
        }
    }

    public function show(SupplyDocument $supply): Response
    {
        //return view('admin.accounting.supply.show', compact('supply'));
        return Inertia::render('Accounting/Supply/Show', [
            'supply' => $this->repository->SupplyWithToArray($supply),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supply = $this->service->create($request->integer('distributor'), $request['stacks']);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function destroy(SupplyDocument $supply): RedirectResponse
    {
        $this->service->destroy($supply);
        return redirect()->back()->with('success', 'Заказ удален успешно');
    }

    public function copy(SupplyDocument $supply): RedirectResponse
    {
        $supply = $this->service->copy($supply);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function payment(SupplyDocument $supply): RedirectResponse
    {
        try {
            $paymentOrder = $this->service->payment($supply);
            return redirect()->route('admin.accounting.payment.show', $paymentOrder);
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function arrival(SupplyDocument $supply): RedirectResponse
    {
        try {
            $arrival = $this->service->arrival($supply);
            return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Документ создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function refund(SupplyDocument $supply): RedirectResponse
    {
        try {
            $refund = $this->service->refund($supply);
            return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Документ сохранен');
        }  catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function completed(SupplyDocument $supply): RedirectResponse
    {
        try {
            $this->service->completed($supply);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function work(SupplyDocument $supply): RedirectResponse
    {
        try {
            $this->service->work($supply);
            return redirect()->back()->with('success', 'Документ в работе. Все связанные документы возвращены в работу!');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_stack(Request $request, OrderItem $item): RedirectResponse
    {
        $request->validate([
            'storage' => 'required|numeric|min:0|not_in:0',
        ]);
        try {
            $stack = $this->service->addStack($item, $request->integer('storage'));
            if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
            return redirect()->back()->with('success', 'Товар ' . $stack->product->name . ' помещен в стек заказа');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function del_stack(SupplyStack $stack): RedirectResponse
    {
        $this->service->delStack($stack);
        return redirect()->back();
    }

    public function add_product(SupplyDocument $supply, Request $request): RedirectResponse
    {
        try {
            $this->service->addProduct($supply, $request->integer('product_id'), $request->integer('quantity'));
            return redirect()->back()->with('success', 'Товары добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(SupplyDocument $supply, Request $request): RedirectResponse
    {
        try {
            $this->service->addProducts($supply, $request->input('products'));
            return redirect()->back()->with('success', 'Товары добавлены');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(SupplyProduct $product): RedirectResponse
    {
        try {
            $this->service->delProduct($product);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function set_product(SupplyProduct $product, Request $request): RedirectResponse
    {
        try {
            $this->service->setProduct($product, $request->integer('quantity'), $request->float('cost'));
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(SupplyDocument $supply, Request $request): RedirectResponse
    {
        try {
            $this->service->setInfo($supply, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
