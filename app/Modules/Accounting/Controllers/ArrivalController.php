<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\ArrivalRepository;
use App\Modules\Accounting\Repository\StackRepository;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;

class ArrivalController extends Controller
{
    private ArrivalService $service;
    private ProductRepository $products;
    private ArrivalRepository $repository;
    private StaffRepository $staffs;

    public function __construct(
        ArrivalService $service,
        ProductRepository $products,
        ArrivalRepository $repository,
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
        $distributors = Distributor::orderBy('name')->get();
        $staffs = $this->staffs->getStaffsChiefs();
        $arrivals = $this->repository->getIndex($request, $filters);

        return view('admin.accounting.arrival.index', compact('arrivals', 'filters', 'distributors', 'staffs'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'distributor' => 'required',
        ]);

        $arrival = $this->service->create((int)$request['distributor']);
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function show(ArrivalDocument $arrival)
    {
        $info = $arrival->getInfoData();
        return view('admin.accounting.arrival.show', compact('arrival', 'info'));
    }

    public function destroy(ArrivalDocument $arrival)
    {
        $this->service->destroy($arrival);
        return redirect()->back();
    }

    public function add(Request $request, ArrivalDocument $arrival)
    {
        $this->service->add(
            $arrival,
            $request->integer('product_id'),
            $request->integer('quantity')
        );
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function add_products(Request $request, ArrivalDocument $arrival)
    {
        $request->validate([
            'products' => 'required',
        ]);
        $this->service->add_products($arrival, $request['products']);
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function remove_item(ArrivalProduct $item)
    {
        $arrival = $item->document;
        $item->delete();
        return redirect()->route('admin.accounting.arrival.show', $arrival);
    }

    public function completed(ArrivalDocument $arrival)
    {
        $this->service->completed($arrival);
        return redirect()->route('admin.accounting.arrival.index');
    }

    //AJAX
    public function set(Request $request, ArrivalProduct $item)
    {
        $cost_ru = $this->service->set($request, $item);
        return response()->json([
            'cost_ru' => $cost_ru,
            'info' => $item->document->getInfoData(),
        ]);
    }
}
