<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Report\DepartureReport;
use App\Modules\Accounting\Repository\DepartureRepository;
use App\Modules\Accounting\Repository\OrganizationRepository;
use App\Modules\Accounting\Service\DepartureService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use function Symfony\Component\Translation\t;

class DepartureController extends Controller
{
    private DepartureService $service;
    private DepartureRepository $repository;
    private StaffRepository $staffs;
    private OrganizationRepository $organizations;
    private DepartureReport $report;


    public function __construct(
        DepartureService       $service,
        DepartureRepository    $repository,
        StaffRepository        $staffs,
        OrganizationRepository $organizations,
        DepartureReport        $report,
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

    public function index(Request $request): \Inertia\Response
    {
        $storages = Storage::orderBy('name')->get();
        $departures = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();

        return Inertia::render('Accounting/Departure/Index', [
            'departures' => $departures,
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
        $departure = $this->service->create($request->integer('storage'));
        return redirect()->route('admin.accounting.departure.show', $departure)->with('success', 'Документ создан');
    }

    public function show(DepartureDocument $departure, Request $request): \Inertia\Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Departure/Show', [
            'departure' => $this->repository->DepartureWithToArray($departure, $request, $filters),
            'storages' => $storages,
            'filters' => $filters,
            'customers' => $this->organizations->getTraders(),
            'printed' => $this->report->index(),
        ]);
    }

    public function destroy(DepartureDocument $departure): RedirectResponse
    {
        $this->service->destroy($departure);
        return redirect()->back()->with('success', 'Списание помечено на удаление');
    }

    public function restore(DepartureDocument $departure): RedirectResponse
    {
        $this->service->restore($departure);
        return redirect()->back()->with('success', 'Списание восстановлено');
    }

    public function full_destroy(DepartureDocument $departure): RedirectResponse
    {
        $this->service->fullDestroy($departure);
        return redirect()->back()->with('success', 'Списание удалено окончательно');
    }

    public function add_product(Request $request, DepartureDocument $departure): RedirectResponse
    {
        $this->service->addProduct($departure, $request->integer('product_id'), $request->float('quantity'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, DepartureDocument $departure): RedirectResponse
    {
        $this->service->addProducts($departure, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлен');
    }

    public function del_product(DepartureProduct $product): RedirectResponse
    {
        $product->delete();
        return redirect()->back()->with('success', 'Удалено');
    }

    public function completed(DepartureDocument $departure): RedirectResponse
    {
        $this->service->completed($departure);
        return redirect()->back()->with('success', 'Документ проведен');

    }

    public function work(DepartureDocument $departure): RedirectResponse
    {
        $this->service->work($departure);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function set_product(Request $request, DepartureProduct $product): RedirectResponse
    {
        $this->service->setProduct($request, $product);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function set_info(DepartureDocument $departure, Request $request): RedirectResponse
    {
        $this->service->setInfo($departure, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function upload(DepartureDocument $departure, Request $request)
    {
        try {
            $this->service->upload($departure, $request);
            return redirect()->back()->with('success', 'Загружено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete_photo(DepartureDocument $departure, Request $request)
    {
        try {
            $this->service->deletePhoto($departure, $request);
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
