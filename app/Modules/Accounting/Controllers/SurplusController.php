<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SurplusDocument;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Accounting\Report\SurplusReport;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Repository\SurplusRepository;
use App\Modules\Accounting\Service\SurplusService;
use App\Modules\Admin\Repository\StaffRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SurplusController extends Controller
{
    private SurplusService $service;
    private SurplusRepository $repository;
    private StaffRepository $staffs;
    private SurplusReport $report;
    private OrganizationRepository $organizations;

    public function __construct(
        SurplusService         $service,
        SurplusRepository      $repository,
        StaffRepository        $staffs,
        SurplusReport          $report,
        OrganizationRepository $organizations,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
        $this->report = $report;
        $this->organizations = $organizations;
    }

    public function index(Request $request): Response
    {
        $storages = Storage::orderBy('name')->get();
        $surpluses = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();

        return Inertia::render('Accounting/Surplus/Index', [
            'surpluses' => $surpluses,
            'filters' => $filters,
            'storages' => $storages,
            'staffs' => $staffs
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'storage' => 'required',
        ]);
        try {
            $departure = $this->service->create_storage($request->integer('storage'));
            return redirect()->route('admin.accounting.surplus.show', $departure)->with('success', 'Документ создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(SurplusDocument $surplus, Request $request): \Inertia\Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Surplus/Show', [
            'surplus' => $this->repository->SurplusWithToArray($surplus, $request, $filters),
            'storages' => $storages,
            'filters' => $filters,
            'printed' => $this->report->index(),
            'customers' => $this->organizations->getTraders(),
        ]);
    }

    public function destroy(SurplusDocument $surplus): RedirectResponse
    {
        $this->service->destroy($surplus);
        return redirect()->back()->with('success', 'Оприходование помечено на удаление');
    }

    public function restore(SurplusDocument $surplus): RedirectResponse
    {
        $this->service->restore($surplus);
        return redirect()->back()->with('success', 'Оприходование восстановлено');
    }

    public function full_destroy(SurplusDocument $surplus): RedirectResponse
    {
        $this->service->fullDestroy($surplus);
        return redirect()->route('admin.accounting.surplus.index')->with('success', 'Оприходование удалено окончательно');
    }

    public function add_product(Request $request, SurplusDocument $surplus): RedirectResponse
    {
        $this->service->addProduct($surplus, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, SurplusDocument $surplus): RedirectResponse
    {
        $this->service->addProducts($surplus, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлен');
    }

    public function del_product(SurplusProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }

    public function completed(SurplusDocument $surplus): RedirectResponse
    {
        $this->service->completed($surplus);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(SurplusDocument $surplus): RedirectResponse
    {
        $this->service->work($surplus);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function set_product(Request $request, SurplusProduct $product): RedirectResponse
    {
        $this->service->setProduct($request, $product);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_info(SurplusDocument $surplus, Request $request): RedirectResponse
    {
        $this->service->setInfo($surplus, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
