<?php

namespace App\Modules\Product\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ModificationRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\ModificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

class ModificationController extends Controller
{
    private ModificationService $service;
    private ProductRepository $products;
    private ModificationRepository $repository;

    public function __construct(ModificationService $service, ProductRepository $products, ModificationRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:product']);
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $modifications = $this->repository->getIndex($request, $filters);
        return Inertia::render('Product/Modification/Index', [
            'modifications' => $modifications,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'product_id' => 'required',
            'attributes' => 'required|array',
        ]);
        try {
            $modification = $this->service->create($request);
            return redirect()->route('admin.product.modification.show', $modification)->with('success', 'Модификация создана');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Modification $modification): Response
    {
        return Inertia::render('Product/Modification/Show', [
            'modification' => $this->repository->ModificationWithToArray($modification),
        ]);
    }

    public function rename(Request $request, Modification $modification): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $this->service->rename($request, $modification);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Modification $modification): RedirectResponse
    {
        try {
            $this->service->delete($modification);
            return redirect()->back()->with('success', 'Модификация удалена');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function del_product(Request $request, Modification $modification): RedirectResponse
    {
        try {
            $this->service->delProduct($request, $modification);
            return redirect()->back()->with('success', 'Товар убран из модификации');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

//AJAX
//TODO Переделать
    public function search(Request $request): JsonResponse
    {
        $result = [];
        $products = [];
        if (empty($request['action'])) {
            $products = $this->products->search($request['search'], 100000);
        } else {
            if ($request['action'] == 'index') {
                $product_in = $this->repository->getAllIdsArray();
                if (!empty($product_in)) $products = $this->products->search($request['search'], 100000, $product_in);
            }
            if ($request['action'] == 'create') {
                $product_in = $this->repository->getAllIdsArray();
                $products = $this->products->search($request['search'], 100000, $product_in, false);
            }
            if ($request['action'] == 'show') {
                $product_in = $this->repository->getAssignmentIdsArray();
                $products = $this->products->search($request['search'], 100000, $product_in, false);
            }
        }

        //TODO Сделать фильтрацию по товарам которые есть в любой модификации (получить все id из ModiRepository и перебрать и проверить in_array($product->id, $array_mod_ids)
        /** @var Product $product */
        foreach ($products as $product) {
            if (is_null($product->modification)) {
                $result[] = $product->toArrayForSearch();
            } else {
                if ($request['action'] == 'index') {
                    $other = route('admin.product.modification.show', $product->modification);
                } else {
                    $other = $product->modification->id;
                }
                $result[] = array_merge($product->toArrayForSearch(), ['other' => $other]);
            }
        }
        return \response()->json($result);
    }

    public function add_product(Request $request, Modification $modification): RedirectResponse
    {
        try {
            $this->service->addProduct($request, $modification);
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
