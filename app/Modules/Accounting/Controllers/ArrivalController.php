<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseItem;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\ArrivalRepository;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Service\ArrivalExpenseService;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class ArrivalController extends Controller
{
    private ArrivalService $service;
    private ArrivalRepository $repository;
    private StaffRepository $staffs;
    private ArrivalExpenseService $expenseService;

    public function __construct(
        ArrivalService        $service,
        ArrivalExpenseService $expenseService,
        ArrivalRepository     $repository,
        StaffRepository       $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
        $this->expenseService = $expenseService;
    }

    public function index(Request $request): Response
    {
        $distributors = Distributor::orderBy('name')->get();
        $staffs = $this->staffs->getStaffsChiefs();
        $arrivals = $this->repository->getIndex($request, $filters);

        return Inertia::render('Accounting/Arrival/Index', [
            'arrivals' => $arrivals,
            'filters' => $filters,
            'distributors' => $distributors,
            'staffs' => $staffs,
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

    public function destroy(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->destroy($arrival);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->with('error', $e->getMessage());
        }
    }

    //На основании: ====>
    public function expense(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $expense = $this->service->expense($arrival);
            return redirect()->route('admin.accounting.arrival.expense.show', $expense);
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function movement(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $movement = $this->service->movement($arrival);
            return redirect()->route('admin.accounting.movement.show', $movement)->with('success', 'Документ сохранен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function pricing(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $pricing = $this->service->pricing($arrival);
            return redirect()->route('admin.accounting.pricing.show', $pricing)->with('success', 'Документ сохранен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function refund(ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $refund = $this->service->refund($arrival);
            return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Документ сохранен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    //<====

    public function add_product(Request $request, ArrivalDocument $arrival): RedirectResponse
    {
        try {
            $this->service->addProduct($arrival, $request->integer('product_id'), $request->integer('quantity'));
            return redirect()->back()->with('success', 'Товар добавлен');
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
            return redirect()->back()->with('success', 'Товары добавлены');
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

    //Доп.Расходы
    public function expense_show(ArrivalExpenseDocument $expense): Response
    {
        return Inertia::render('Accounting/Arrival/Expense/Show', [
            'expense' => $this->repository->ExpenseWithToArray($expense),
        ]);
    }

    public function expense_set_info(ArrivalExpenseDocument $expense, Request $request): RedirectResponse
    {
        try {
            $this->expenseService->setInfo($expense, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function expense_add_item(Request $request, ArrivalExpenseDocument $expense): RedirectResponse
    {
        try {
            $this->expenseService->addItem($expense, $request);
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function expense_set_item(ArrivalExpenseItem $item, Request $request): RedirectResponse
    {
        try {
            $this->expenseService->setItem($item, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function expense_del_item(ArrivalExpenseItem $item): RedirectResponse
    {
        try {
            $this->expenseService->delItem($item);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
