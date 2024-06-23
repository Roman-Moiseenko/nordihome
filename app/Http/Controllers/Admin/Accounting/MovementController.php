<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

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
        return $this->try_catch_admin(function () use($request) {
            $query = MovementDocument::orderByDesc('created_at');
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';
            if ($completed == 'draft') $query->where('status', MovementDocument::STATUS_DRAFT);
            if ($completed == 'departure') $query->where('status', MovementDocument::STATUS_DEPARTURE);
            if ($completed == 'arrival') $query->where('status', MovementDocument::STATUS_ARRIVAL);
            if ($completed == 'completed') $query->where('status', MovementDocument::STATUS_COMPLETED);

            if (!empty($storage_in = $request->get('storage_in'))) {
                $query->where('storage_in', $storage_in);
            }
            if (!empty($storage_out = $request->get('storage_out'))) {
                $query->where('storage_out', $storage_out);
            }

            $movements = $this->pagination($query, $request, $pagination);
            return view('admin.accounting.movement.index',
                compact('movements', 'pagination', 'completed', 'storages', 'storage_in', 'storage_out'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $storages = Storage::get();
            return view('admin.accounting.movement.create', compact('storages'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage_out' => 'required',
            'storage_in' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $movement = $this->service->create($request->only(['number', 'storage_out', 'storage_in']));
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function show(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $info = $movement->getInfoData();
            return view('admin.accounting.movement.show', compact('movement', 'info'));
        });
    }

    public function edit(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $storages = Storage::get();
            return view('admin.accounting.movement.edit', compact('movement'), compact('storages'));
        });
    }
/*
    public function update(Request $request, MovementDocument $movement)
    {
        $request->validate([
            'storage_in' => 'required',
            'storage_out' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request, $movement) {
            $movement = $this->service->update($request, $movement);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }*/

    public function destroy(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $this->service->destroy($movement);
            return redirect()->back();
        });
    }

    public function add(Request $request, MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($request, $movement) {
            $this->service->add($movement, (int)$request['product_id'], (int)$request['quantity']);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function add_products(Request $request, MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($request, $movement) {
            $this->service->add_products($movement, $request['products']);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }


    public function remove_item(MovementProduct $item)
    {
        return $this->try_catch_admin(function () use($item) {
            $movement = $item->document;
            $item->delete();
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function activate(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $this->service->activate($movement);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function departure(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $this->service->departure($movement);
            return redirect()->route('admin.accounting.movement.show', $movement);
        });
    }

    public function arrival(MovementDocument $movement)
    {
        return $this->try_catch_admin(function () use($movement) {
            $this->service->arrival($movement);
            return redirect()->route('admin.accounting.movement.index');
        });
    }

    //AJAX
    public function set(Request $request, MovementProduct $item)
    {
        return $this->try_catch_ajax_admin(function () use($request, $item) {
            $result = $this->service->set($request, $item);
            return response()->json($result);
        });
    }

    public function search(Request $request, MovementDocument $movement)
    {
        return $this->try_catch_ajax_admin(function () use($request, $movement) {
            $result = [];
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$movement->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
            return \response()->json($result);
        });
    }

}
