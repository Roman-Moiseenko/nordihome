<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\ArrivalRepository;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class ArrivalController extends Controller
{
    private ArrivalService $service;
    private ProductRepository $products;
    private ArrivalRepository $repository;
    private StaffRepository $staffs;

    public function __construct(
        ArrivalService    $service,
        ProductRepository $products,
        ArrivalRepository $repository,
        StaffRepository   $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work']);
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request): Response
    {
        $distributors = Distributor::orderBy('name')->get();
        $staffs = $this->staffs->getStaffsChiefs();
        $arrivals = $this->repository->getIndex($request, $filters);

        //return view('admin.accounting.arrival.index', compact('arrivals', 'filters', 'distributors', 'staffs'));
        return Inertia::render('Accounting/Arrival/Index', [
            'arrivals' => $arrivals,
            'filters' => $filters,
            'distributors' => $distributors,
            'staffs' => $staffs
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'distributor' => 'required',
        ]);
        try {
            $arrival = $this->service->create($request->integer('distributor'));
            return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Приходная накладная создана');
        } catch (\DomainException $e) {
            return redirect()->with('error', $e->getMessage());
        }
    }

    public function show(ArrivalDocument $arrival): Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Arrival/Show', [
            'arrival' => $this->repository->ArrivalWithToArray($arrival),
            'storages' => $storages,
            'operations' => $this->repository->getOperations(),
        ]);
    }

    //На основании: ====>
    public function expenses(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $expenses = $this->service->expenses($arrival);
            return redirect()->route('admin.accounting.expenses.show', $expenses)->with('success', 'Документ сохранен');
        }  catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function movement(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $movement = $this->service->movement($arrival);
            return redirect()->route('admin.accounting.movement.show', $movement)->with('success', 'Документ сохранен');
        }  catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function invoice(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $invoice = $this->service->expenses($arrival);
            return redirect()->route('admin.accounting.invoice.show', $invoice)->with('success', 'Документ сохранен');
        }  catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function refund(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $refund = $this->service->expenses($arrival);
            return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Документ сохранен');
        }  catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    //<====

    public function destroy(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->destroy($arrival);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->with('error', $e->getMessage());
        }
    }

    public function add_product(Request $request, ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->addProduct($arrival, $request->integer('product_id'), $request->integer('quantity'));
            return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(Request $request, ArrivalDocument $arrival): RedirectResponse
    {
        $request->validate([
            'products' => 'required',
        ]);
        try {
            $this->service->addProducts($arrival, $request->input('products'));
            return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Товары добавлены');
        } catch (\DomainException $e) {
            return redirect()->with('error', $e->getMessage());
        }
    }

    public function completed(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->completed($arrival);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function work(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->work($arrival);
            return redirect()->back()->with('success', 'Документ в работе');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(ArrivalDocument $arrival, Request $request): RedirectResponse
    {
        try {
            $this->service->setInfo($arrival, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_product(ArrivalProduct $product, Request $request): RedirectResponse
    {
        try {
            $this->service->setProduct($product, $request->integer('quantity'), $request->float('cost'));
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(ArrivalProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }
}
