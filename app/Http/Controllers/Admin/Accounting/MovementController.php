<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class MovementController extends Controller
{
    private MovementService $service;
    private ProductRepository $products;
    private mixed $pagination;

    public function __construct(MovementService $service, ProductRepository $products)
    {
        $this->service = $service;
        $this->products = $products;
        $this->pagination = Config::get('shop-config.p-list');

    }

    public function index(Request $request)
    {
        try {
            $query = MovementDocument::orderByDesc('created_at');
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';
            if ($completed == 'true') $query->where('completed', '=', true);
            if ($completed == 'false') $query->where('completed', '=', false);

            if (!empty($storage_in = $request->get('storage_in'))) {
                $query->where('storage_in', $storage_in);
            }
            if (!empty($storage_out = $request->get('storage_out'))) {
                $query->where('storage_out', $storage_out);
            }
            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $movements = $query->paginate($pagination);
                $movements->appends(['p' => $pagination]);
            } else {
                $movements = $query->paginate($this->pagination);
            }
            return view('admin.accounting.movement.index',
                compact('movements', 'pagination', 'completed', 'storages', 'storage_in', 'storage_out'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            $storages = Storage::get();
            return view('admin.accounting.movement.create', compact('storages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'storage_out' => 'required',
            'storage_in' => 'required',
        ]);
        try {
            $movement = $this->service->create($request);
            return redirect()->route('admin.accounting.movement.show', $movement);

        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(MovementDocument $movement)
    {
        try {
            $info = $movement->getInfoData();
            return view('admin.accounting.movement.show', compact('movement', 'info'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(MovementDocument $movement)
    {
        try {
            $storages = Storage::get();
            return view('admin.accounting.movement.edit', compact('movement'), compact('storages'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, MovementDocument $movement)
    {
        try {
            $request->validate([
                'storage_in' => 'required',
                'storage_out' => 'required',
            ]);
            $movement = $this->service->update($request, $movement);
            return redirect()->route('admin.accounting.movement.show', $movement);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(MovementDocument $movement)
    {
        try {
            $this->service->destroy($movement);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function add(Request $request, MovementDocument $movement)
    {
        try {
            $this->service->add($request, $movement);
            return redirect()->route('admin.accounting.movement.show', $movement);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function remove_item(MovementProduct $item)
    {
        try {
            $movement = $item->document;
            $item->delete();
            return redirect()->route('admin.accounting.movement.show', $movement);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function completed(MovementDocument $movement)
    {
        try {
            $this->service->completed($movement);
            return redirect()->route('admin.accounting.movement.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    //AJAX
    public function set(Request $request, MovementProduct $item)
    {
        try {
            $result = $this->service->set($request, $item);
            return response()->json($result);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function search(Request $request, MovementDocument $movement)
    {
        $result = [];
        try {
            $products = $this->products->search($request['search']);
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$movement->isProduct($product->id)) {
                    $result[] = $this->products->toArrayForSearch($product);
                }
            }
        } catch (\Throwable $e) {
            $result = [$e->getMessage(), $e->getFile(), $e->getLine()];
            event(new ThrowableHasAppeared($e));
        }
        return \response()->json($result);
    }

}
