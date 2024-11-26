<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\DepartureRepository;
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


    public function __construct(
        DepartureService    $service,
        DepartureRepository $repository,
        StaffRepository     $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
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
        try {
            $departure = $this->service->create((int)$request['storage']);
            return redirect()->route('admin.accounting.departure.show', $departure)->with('success', 'Документ создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(DepartureDocument $departure, Request $request): \Inertia\Response
    {
        $storages = Storage::orderBy('name')->getModels();
        return Inertia::render('Accounting/Departure/Show', [
            'departure' => $this->repository->DepartureWithToArray($departure, $request),
            'storages' => $storages,
        ]);
    }

    public function destroy(DepartureDocument $departure): RedirectResponse
    {
        try {
            $this->service->destroy($departure);
            return redirect()->back()->with('success', 'Документ удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_product(Request $request, DepartureDocument $departure): RedirectResponse
    {
        try {
            $this->service->addProduct($departure, $request->integer('product_id'), $request->integer('quantity'));
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add_products(Request $request, DepartureDocument $departure): RedirectResponse
    {
        try {
            $this->service->addProducts($departure, $request->input('products'));
            return redirect()->back()->with('success', 'Товары добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(DepartureProduct $product): RedirectResponse
    {
        try {
            $product->delete();
            return redirect()->back()->with('success', 'Удалено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function completed(DepartureDocument $departure): RedirectResponse
    {
        try {
            $this->service->completed($departure);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function work(DepartureDocument $departure): RedirectResponse
    {
        try {
            $this->service->work($departure);
            return redirect()->back()->with('success', 'Документ проведен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_product(Request $request, DepartureProduct $product): RedirectResponse
    {
        try {
            $this->service->setProduct($request, $product);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(DepartureDocument $departure, Request $request): RedirectResponse
    {
        try {
            $this->service->setInfo($departure, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
