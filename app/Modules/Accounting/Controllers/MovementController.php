<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\Deprecated;

class MovementController extends Controller
{
    private MovementService $service;
    private ProductRepository $products;

    public function __construct(MovementService $service, ProductRepository $products)
    {
        $this->middleware(['auth:admin', 'can:accounting']);
        $this->service = $service;
        $this->products = $products;
    }

    public function index(Request $request)
    {
        $storages = Storage::orderBy('name')->get();
        return view('admin.accounting.movement.index', compact('storages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage_out' => 'required',
            'storage_in' => 'required',
        ]);
        $movement = $this->service->create(
            $request->integer('storage_out'),
            $request->integer('storage_in')
        );
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function show(MovementDocument $movement)
    {
        $info = $movement->getInfoData();
        return view('admin.accounting.movement.show', compact('movement', 'info'));
    }

    public function destroy(MovementDocument $movement)
    {
        $this->service->destroy($movement);
        return redirect()->back();
    }

    public function add(Request $request, MovementDocument $movement)
    {
        $this->service->add($movement, (int)$request['product_id'], (int)$request['quantity']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function add_products(Request $request, MovementDocument $movement)
    {
        $this->service->add_products($movement, $request['products']);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }


    public function remove_item(MovementProduct $item)
    {
        $movement = $item->document;
        $item->delete();
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function activate(MovementDocument $movement)
    {
        $this->service->activate($movement);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function departure(MovementDocument $movement)
    {
        $this->service->departure($movement);
        return redirect()->route('admin.accounting.movement.show', $movement);
    }

    public function arrival(MovementDocument $movement)
    {
        $this->service->arrival($movement);
        return redirect()->route('admin.accounting.movement.index');
    }

    //AJAX
    public function set(Request $request, MovementProduct $item)
    {
        $result = $this->service->set($request, $item);
        return response()->json($result);
    }

    public function search(Request $request, MovementDocument $movement)
    {
        $result = [];
        $products = $this->products->search($request['search']);
        /** @var Product $product */
        foreach ($products as $product) {
            if (!$movement->isProduct($product->id)) {
                $result[] = $this->products->toArrayForSearch($product);
            }
        }
        return \response()->json($result);
    }

}
