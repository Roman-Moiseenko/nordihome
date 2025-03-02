<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Report\RefundReport;
use App\Modules\Accounting\Repository\RefundRepository;
use App\Modules\Accounting\Service\RefundService;
use App\Modules\Admin\Repository\StaffRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RefundController extends Controller
{
    private RefundService $service;
    private RefundRepository $repository;
    private StaffRepository $staffs;
    private RefundReport $report;

    public function __construct(
        RefundService    $service,
        RefundRepository $repository,
        StaffRepository  $staffs,
        RefundReport     $report,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
        $this->report = $report;
    }

    public function index(Request $request): \Inertia\Response
    {
        $distributors = Distributor::orderBy('name')->get();
        $staffs = $this->staffs->getStaffsChiefs();
        $refunds = $this->repository->getIndex($request, $filters);
        return Inertia::render('Accounting/Refund/Index', [
            'refunds' => $refunds,
            'filters' => $filters,
            'distributors' => $distributors,
            'staffs' => $staffs
        ]);
    }

    public function show(RefundDocument $refund, Request $request): Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Refund/Show', [
            'refund' => $this->repository->RefundWithToArray($refund, $request, $filters),
            'storages' => $storages,
            'filters' => $filters,
            'printed' => $this->report->index(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['distributor' => 'required',]);
        $refund = $this->service->create($request->integer('distributor_id'));
        return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Документ создан');
    }

    public function completed(RefundDocument $refund): RedirectResponse
    {
        $this->service->completed($refund);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(RefundDocument $refund): RedirectResponse
    {
        $this->service->work($refund);
        return redirect()->back()->with('success', 'Документ возвращен в работу');
    }

    public function set_info(RefundDocument $refund, Request $request): RedirectResponse
    {
        $this->service->setInfo($refund, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function add_product(RefundDocument $refund, Request $request): RedirectResponse
    {
        $this->service->addProduct($refund, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Товар добавлен');
    }

    public function add_products(RefundDocument $refund, Request $request): RedirectResponse
    {
        $request->validate([
            'products' => 'required',
        ]);
        $this->service->addProducts($refund, $request->input('products'));
        return redirect()->route('admin.accounting.refund.show', $refund)->with('success', 'Товары добавлены');
    }

    public function set_product(RefundProduct $product, Request $request): RedirectResponse
    {
        $this->service->setProduct($product, $request->float('quantity'));
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_product(RefundProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }

    public function destroy(RefundDocument $refund): RedirectResponse
    {
        $this->service->destroy($refund);
        return redirect()->back()->with('success', 'Возврат помечен на удаление');
    }

    public function restore(RefundDocument $refund): RedirectResponse
    {
        $this->service->restore($refund);
        return redirect()->back()->with('success', 'Возврат восстановлен');
    }

    public function full_destroy(RefundDocument $refund): RedirectResponse
    {
        $this->service->fullDestroy($refund);
        return redirect()->back()->with('success', 'Возврат удален окончательно');
    }
}
