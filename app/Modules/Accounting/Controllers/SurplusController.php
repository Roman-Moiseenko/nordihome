<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SurplusDocument;
use App\Modules\Accounting\Entity\SurplusProduct;
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

    public function __construct(
        SurplusService    $service,
        SurplusRepository $repository,
        StaffRepository   $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
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
            'surplus' => $this->repository->SurplusWithToArray($surplus, $request),
            'storages' => $storages,
        ]);
    }

    public function destroy(SurplusDocument $surplus): RedirectResponse
    {
        try {
            $this->service->destroy($surplus);
            return redirect()->back()->with('success', 'Документ удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_product(Request $request, SurplusDocument $surplus): RedirectResponse
    {
        try {
            $this->service->addProduct($surplus, $request->integer('product_id'), $request->float('quantity'));
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(Request $request, SurplusDocument $surplus): RedirectResponse
    {
        try {
            $this->service->addProducts($surplus, $request->input('products'));
            return redirect()->back()->with('success', 'Товары добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(SurplusProduct $product): RedirectResponse
    {
        try {
            $product->delete();
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function completed(SurplusDocument $surplus): RedirectResponse
    {
        try {
            $this->service->completed($surplus);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function work(SurplusDocument $surplus): RedirectResponse
    {
        try {
            $this->service->work($surplus);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_product(Request $request, SurplusProduct $product): RedirectResponse
    {
        try {
            $this->service->setProduct($request, $product);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(SurplusDocument $surplus, Request $request): RedirectResponse
    {
        try {
            $this->service->setInfo($surplus, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
