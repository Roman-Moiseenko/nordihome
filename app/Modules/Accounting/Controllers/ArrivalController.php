<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseItem;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Report\ArrivalReport;
use App\Modules\Accounting\Repository\ArrivalRepository;
use App\Modules\Accounting\Service\ArrivalExpenseService;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Admin\Repository\StaffRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArrivalController extends Controller
{
    private ArrivalService $service;
    private ArrivalRepository $repository;
    private StaffRepository $staffs;
    private ArrivalExpenseService $expenseService;
    private ArrivalReport $report;

    public function __construct(
        ArrivalService        $service,
        ArrivalExpenseService $expenseService,
        ArrivalRepository     $repository,
        StaffRepository       $staffs,
        ArrivalReport         $report,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
        $this->expenseService = $expenseService;
        $this->report = $report;
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

    public function show(ArrivalDocument $arrival, Request $request): Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Arrival/Show', [
            'arrival' => $this->repository->ArrivalWithToArray($arrival, $request, $filters),
            'filters' => $filters,
            'storages' => $storages,
            'operations' => $this->repository->getOperations(),
            'printed' => $this->report->index(),
        ]);
    }

    public function completed(ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->completed($arrival);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->work($arrival);
        return redirect()->back()->with('success', 'Документ в работе');
    }

    public function set_info(ArrivalDocument $arrival, Request $request): RedirectResponse
    {
        $this->service->setInfo($arrival, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->destroy($arrival);
        return redirect()->back()->with('success', 'Поступление помечено на удаление');
    }

    public function restore(ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->restore($arrival);
        return redirect()->back()->with('success', 'Поступление восстановлено');
    }

    public function full_destroy(ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->fullDestroy($arrival);
        return redirect()->route('admin.accounting.arrival.index')->with('success', 'Поступление удалено окончательно');
    }

    //На основании: ====>
    public function expense(ArrivalDocument $arrival): RedirectResponse
    {
        $expense = $this->service->expense($arrival);
        return redirect()->route('admin.accounting.arrival.expense.show', $expense);
    }

    public function movement(ArrivalDocument $arrival): RedirectResponse
    {
        $movement = $this->service->movement($arrival);
        return redirect()->route('admin.accounting.movement.show', $movement)->with('success', 'Документ сохранен');
    }

    public function pricing(ArrivalDocument $arrival): RedirectResponse
    {
        $pricing = $this->service->pricing($arrival);
        return redirect()->route('admin.accounting.pricing.show', $pricing)->with('success', 'Документ сохранен');
    }

    public function refund(ArrivalDocument $arrival): RedirectResponse
    {
        $refund = $this->service->refund($arrival);
        return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Документ сохранен');
    }

    //<====
    public function add_product(Request $request, ArrivalDocument $arrival): RedirectResponse
    {
        $this->service->addProduct($arrival, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, ArrivalDocument $arrival): RedirectResponse
    {
        $request->validate([
            'products' => 'required',
        ]);
        $this->service->addProducts($arrival, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function set_product(ArrivalProduct $product, Request $request): RedirectResponse
    {
        $this->service->setProduct($product, $request->float('quantity'), $request->float('cost'));
        return redirect()->back()->with('success', 'Сохранено');
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
        $this->expenseService->setInfo($expense, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function expense_add_item(Request $request, ArrivalExpenseDocument $expense): RedirectResponse
    {
        $this->expenseService->addItem($expense, $request);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function expense_set_item(ArrivalExpenseItem $item, Request $request): RedirectResponse
    {
        $this->expenseService->setItem($item, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function expense_del_item(ArrivalExpenseItem $item): RedirectResponse
    {
        $this->expenseService->delItem($item);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function expense_destroy(ArrivalExpenseDocument $expense): RedirectResponse
    {
        $arrival = $expense->arrival;
        $expense->delete();
        return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Доп.расходы помечены на удаление');
    }

    public function expense_restore(ArrivalExpenseDocument $expense): RedirectResponse
    {
        $this->service->restore($expense);
        return redirect()->back()->with('success', 'Доп.расходы восстановлены');
    }

    public function expense_full_destroy(ArrivalExpenseDocument $expense): RedirectResponse
    {
        $arrival = $expense->arrival;
        $this->service->fullDestroy($expense);
        return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Доп.расходы удалены окончательно');
    }
}
