<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Service\ModificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ModificationController extends Controller
{

    private mixed $pagination;
    private ModificationService $service;

    public function __construct(ModificationService $service)
    {
        $this->pagination = Config::get('shop-config.p-list');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Modification::orderBy('name');
        if (!empty($name = $request['name'])) {
            $query = $query->where('name','LIKE',"%{$name}%");
        }
        $modification = $query->paginate($this->pagination);
        return view('admin.product.modification.index', compact('modification'));
    }


    public function create(Request $request)
    {
        //По ссылке из товара
        $product_id = $request['product_id'] ?? null;
        return view('admin.product.modification.create', compact('product_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'integer|required|unique',
        ]);
        $modification = $this->service->create($request);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }

    public function show(Modification $modification)
    {
        return view('admin.product.modification.show', compact('modification'));
    }

    public function edit(Modification $modification)
    {
        return view('admin.product.modification.edit', compact('modification'));

    }

    public function set_modifications(Request $request, Modification $modification)
    {
        $modification = $this->service->set_modifications($request, $modification);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }


    public function update(Request $request, Modification $modification)
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'integer|required',
        ]);
        $modification = $this->service->update($request, $modification);
        return redirect()->route('admin.product.modification.show', compact('modification'));
    }

    public function destroy(Modification $modification)
    {
        try {
            $this->service->delete($modification);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.product.modification.index');
    }
}
