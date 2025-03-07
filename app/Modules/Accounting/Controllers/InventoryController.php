<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\InventoryProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Report\InventoryReport;
use App\Modules\Accounting\Repository\InventoryRepository;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Service\InventoryService;
use App\Modules\Admin\Repository\StaffRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    private InventoryService $service;
    private InventoryRepository $repository;
    private StaffRepository $staffs;
    private OrganizationRepository $organizations;
    private InventoryReport $report;

    public function __construct(
        InventoryService       $service,
        InventoryRepository    $repository,
        StaffRepository        $staffs,
        OrganizationRepository $organizations,
        InventoryReport        $report,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;

        $this->staffs = $staffs;
        $this->organizations = $organizations;
        $this->report = $report;
    }

    public function index(Request $request): Response
    {
        $staffs = $this->staffs->getStaffsChiefs();
        $inventories = $this->repository->getIndex($request, $filters);
        $storages = Storage::orderBy('name')->getModels();

        return Inertia::render('Accounting/Inventory/Index', [
            'inventories' => $inventories,
            'filters' => $filters,
            'staffs' => $staffs,
            'storages' => $storages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'storage_id' => 'required|numeric',
        ]);
        try {
            $inventory = $this->service->create($request->integer('storage_id'));
            return redirect()->route('admin.accounting.inventory.show', $inventory)->with('success', 'Инвентаризация создана');
        } catch (\DomainException $e) {
            return redirect()->with('error', $e->getMessage());
        }
    }

    public function show(InventoryDocument $inventory, Request $request): Response
    {
        return Inertia::render('Accounting/Inventory/Show', [
            'inventory' => $this->repository->InventoryWithToArray($inventory, $request, $filters),
            'filters' => $filters,
            'customers' => $this->organizations->getTraders(),
            'printed' => $this->report->index(),
        ]);
    }

    public function completed(InventoryDocument $inventory): RedirectResponse
    {
        $this->service->completed($inventory);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(InventoryDocument $inventory): RedirectResponse
    {
        $this->service->work($inventory);
        return redirect()->back()->with('success', 'Документ в работе');
    }

    public function set_info(InventoryDocument $inventory, Request $request): RedirectResponse
    {
        $this->service->setInfo($inventory, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(InventoryDocument $inventory): RedirectResponse
    {
        $this->service->destroy($inventory);
        return redirect()->route('admin.accounting.inventory.index')->with('success', 'Инвентаризация помечена на удаление');
    }

    public function restore(InventoryDocument $inventory): RedirectResponse
    {
        $this->service->restore($inventory);
        return redirect()->back()->with('success', 'Инвентаризация восстановлена');
    }

    public function full_destroy(InventoryDocument $inventory): RedirectResponse
    {
        $this->service->fullDestroy($inventory);
        return redirect()->back()->with('success', 'Инвентаризация удалено окончательно');
    }


    public function add_product(Request $request, InventoryDocument $inventory): RedirectResponse
    {
        $this->service->addProduct($inventory, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, InventoryDocument $inventory): RedirectResponse
    {
        $request->validate([
            'products' => 'required',
        ]);
        $this->service->addProducts($inventory, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function set_product(InventoryProduct $product, Request $request): RedirectResponse
    {
        $this->service->setProduct($product, $request->float('quantity'));
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_product(InventoryProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }
}
