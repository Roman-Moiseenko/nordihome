<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Report\SupplyReport;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Repository\SupplyRepository;
use App\Modules\Accounting\Service\SupplyService;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;


class SupplyController extends Controller
{
    private SupplyService $service;
    private StackRepository $stacks;
    private SupplyRepository $repository;
    private StaffRepository $staffs;
    private SupplyReport $report;
    private OrganizationRepository $organizations;

    public function __construct(
        SupplyService          $service,
        StackRepository        $stacks,
        SupplyRepository       $repository,
        StaffRepository        $staffs,
        SupplyReport           $report,
        OrganizationRepository $organizations,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting'])->except(['work', 'destroy']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->stacks = $stacks;
        $this->repository = $repository;
        $this->staffs = $staffs;
        $this->report = $report;
        $this->organizations = $organizations;
    }

    public function index(Request $request): Response
    {
        $distributors = Distributor::orderBy('name')->getModels();
        $stack_count = SupplyStack::where('supply_id', null)->count();
        $supplies = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();
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
        //dd($stacks);
        if (!empty($stacks)) { //Если стек не пуст, то показываем
            return Inertia::render('Accounting/Supply/Create', [
                'stacks' => $stacks,
                'distributor' => $distributor,
            ]);
        } else { //Иначе создаем пустой заказ
            $supply = $this->service->createEmpty($distributor);
            return redirect()->route('admin.accounting.supply.show', $supply);
        }
    }

    public function show(SupplyDocument $supply, Request $request): Response
    {
        return Inertia::render('Accounting/Supply/Show', [
            'supply' => $this->repository->SupplyWithToArray($supply, $request, $filters),
            'filters' => $filters,
            'printed' => $this->report->index(),
            'customers' => $this->organizations->getTraders(),
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
        return redirect()->back()->with('success', 'Заказ помечен на удаление');
    }

    public function restore(SupplyDocument $supply): RedirectResponse
    {
        $this->service->restore($supply);
        return redirect()->back()->with('success', 'Заказ восстановлен');
    }

    public function full_destroy(SupplyDocument $supply): RedirectResponse
    {
        $this->service->fullDestroy($supply);
        return redirect()->route('admin.accounting.supply.index')->with('success', 'Заказ удален окончательно');
    }

    public function copy(SupplyDocument $supply): RedirectResponse
    {
        $supply = $this->service->copy($supply);
        return redirect()->route('admin.accounting.supply.show', $supply);
    }

    public function payment(SupplyDocument $supply): RedirectResponse
    {
        $paymentOrder = $this->service->payment($supply);
        return redirect()->route('admin.accounting.payment.show', $paymentOrder);
    }

    public function arrival(SupplyDocument $supply): RedirectResponse
    {
        $arrival = $this->service->arrival($supply);
        return redirect()->route('admin.accounting.arrival.show', $arrival)->with('success', 'Документ создан');
    }

    public function completed(SupplyDocument $supply): RedirectResponse
    {
        $this->service->completed($supply);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(SupplyDocument $supply): RedirectResponse
    {
        $this->service->work($supply);
        return redirect()->back()->with('success', 'Документ в работе. Все связанные документы возвращены в работу!');
    }

    public function add_stack(Request $request, OrderItem $item): RedirectResponse
    {

        $stack = $this->service->addStack($item, $request->input('storage'));
        if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
        return redirect()->back()->with('success', 'Товар ' . $stack->product->name . ' помещен в стек заказа');
    }

    public function del_stack(SupplyStack $stack): RedirectResponse
    {
        $this->service->delStack($stack);
        return redirect()->back();
    }

    public function add_product(SupplyDocument $supply, Request $request): RedirectResponse
    {
        $this->service->addProduct($supply, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Товары добавлен');
    }

    public function add_products(SupplyDocument $supply, Request $request): RedirectResponse
    {
        $this->service->addProducts($supply, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(SupplyProduct $product): RedirectResponse
    {
        $this->service->delProduct($product);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function set_product(SupplyProduct $product, Request $request): RedirectResponse
    {
        $this->service->setProduct($product, $request->float('quantity'), $request->float('cost'));
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_info(SupplyDocument $supply, Request $request): RedirectResponse
    {
        $this->service->setInfo($supply, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
