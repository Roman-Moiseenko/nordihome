<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Report\MovementReport;
use App\Modules\Accounting\Repository\MovementRepository;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\UseCase\PaginationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\Deprecated;

class MovementController extends Controller
{
    private MovementService $service;
    private StaffRepository $staffs;
    private MovementRepository $repository;
    private MovementReport $report;

    public function __construct(
        MovementService    $service,
        StaffRepository    $staffs,
        MovementRepository $repository,
        MovementReport     $report,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->staffs = $staffs;
        $this->repository = $repository;
        $this->report = $report;
    }

    public function index(Request $request): Response
    {
        $storages = Storage::orderBy('name')->get()->toArray();
        $staffs = $this->staffs->getStaffsChiefs();
        $movements = $this->repository->getIndex($request, $filters);
        $statuses = MovementDocument::STATUSES;

        return Inertia::render('Accounting/Movement/Index', [
            'movements' => $movements,
            'filters' => $filters,
            'storages' => $storages,
            'staffs' => $staffs,
            'statuses' => $statuses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'storage_out' => 'required',
            'storage_in' => 'required',
        ]);
        $movement = $this->service->create(
            $request->integer('storage_out'),
            $request->integer('storage_in')
        );
        return redirect()->route('admin.accounting.movement.show', $movement)->with('success', 'Документ сохранен');;
    }

    public function show(MovementDocument $movement, Request $request): Response
    {
        $storages = Storage::orderBy('name')->get()->toArray();
        return Inertia::render('Accounting/Movement/Show', [
            'movement' => $this->repository->MovementWithToArray($movement, $request, $filters),
            'storages' => $storages,
            'filters' => $filters,
            'printed' => $this->report->index(),
        ]);
    }

    public function set_info(MovementDocument $movement, Request $request): RedirectResponse
    {
        try {
            $this->service->setInfo($movement, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function completed(MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->completed($movement);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function work(MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->work($movement);
            return redirect()->back()->with('success', 'Документ в работе');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->destroy($movement);
            return redirect()->back()->with('success', 'Документ удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function add_product(Request $request, MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->addProduct($movement, $request->integer('product_id'), $request->float('quantity'));
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function add_products(Request $request, MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->addProducts($movement, $request['products']);
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(MovementProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }

    public function departure(MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->departure($movement);
            return redirect()->back()->with('success', 'Товар отмечен как в пути');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function arrival(MovementDocument $movement): RedirectResponse
    {
        try {
            $this->service->arrival($movement);
            return redirect()->back()->with('success', 'Товар поступил на склад');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_product(Request $request, MovementProduct $product): RedirectResponse
    {
        try {
            $this->service->setProduct($request, $product);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
