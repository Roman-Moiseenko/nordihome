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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use function Symfony\Component\Translation\t;

class DepartureController extends Controller
{
    private DepartureService $service;
    private ProductRepository $products;
    private DepartureRepository $repository;
    private StaffRepository $staffs;


    public function __construct(
        DepartureService $service,
        ProductRepository $products,
        DepartureRepository $repository,
        StaffRepository $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request)
    {
        $storages = Storage::orderBy('name')->get();
        $departures = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();
        return view('admin.accounting.departure.index', compact('departures', 'filters', 'staffs', 'storages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage' => 'required',
        ]);
        $departure = $this->service->create((int)$request['storage']);
        return redirect()->route('admin.accounting.departure.show', $departure);
    }

    public function show(DepartureDocument $departure)
    {
        $info = $departure->getInfoData();
        return view('admin.accounting.departure.show', compact('departure', 'info'));
    }

    public function destroy(DepartureDocument $departure)
    {
        $this->service->destroy($departure);
        return redirect()->back();
    }

    public function add(Request $request, DepartureDocument $departure)
    {
        $this->service->add($departure, (int)$request['product_id'], (int)$request['quantity']);
        return redirect()->route('admin.accounting.departure.show', $departure);
    }

    public function add_products(Request $request, DepartureDocument $departure)
    {
        $this->service->add_products($departure, $request['products']);
        return redirect()->route('admin.accounting.departure.show', $departure);
    }

    public function remove_item(DepartureProduct $item)
    {
        $movement = $item->document;
        $item->delete();
        return redirect()->route('admin.accounting.departure.show', $movement);
    }

    public function completed(DepartureDocument $departure)
    {
        $this->service->completed($departure);
        return redirect()->route('admin.accounting.departure.index');
    }

    //AJAX
    public function set(Request $request, DepartureProduct $item)
    {
        $result = $this->service->set($request, $item);
        return response()->json($result);
    }

}
