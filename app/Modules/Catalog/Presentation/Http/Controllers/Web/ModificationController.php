<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Modification\AddProductToModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\CreateModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\IndexModificationQuery;
use App\Modules\Catalog\Application\Actions\Modification\RemoveProductFromModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\RenameModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\SearchModificationCreateQuery;
use App\Modules\Catalog\Application\Actions\Modification\SearchModificationProductQuery;
use App\Modules\Catalog\Application\Actions\Modification\SetPrimaryModificationProductUseCase;
use App\Modules\Catalog\Application\Actions\Modification\ViewModificationQuery;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationCreateData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationRenameData;
use App\Modules\Catalog\Infrastructure\Models\Modification;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Repository\ModificationRepository;
use App\Modules\Catalog\Repository\ProductRepository;
use App\Modules\Catalog\Service\ModificationService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModificationController extends Controller
{
    private ModificationService $service;
    private ProductRepository $products;
    private ModificationRepository $repository;

    public function __construct(
        ModificationService                        $service,
        ProductRepository                          $products,
        ModificationRepository                     $repository,
        private readonly CreateModificationUseCase $createModificationUseCase,
        private readonly IndexModificationQuery    $indexModificationQuery,
        private readonly SearchModificationCreateQuery $searchModificationCreateQuery,
        private readonly SearchModificationProductQuery $searchModificationProductQuery,
        private readonly AddProductToModificationUseCase $addProductToModificationUseCase,
        private readonly RemoveProductFromModificationUseCase $removeProductFromModificationUseCase,
        private readonly SetPrimaryModificationProductUseCase $setPrimaryModificationProductUseCase,
        private readonly RenameModificationUseCase $renameModificationUseCase,
        private readonly ViewModificationQuery     $viewModificationQuery,
    )
    {
        $this->service = $service;
        $this->products = $products;
        $this->repository = $repository;
    }

    public function index(Request $request, UserPermission $permission): Response
    {
        $modifications = $this->indexModificationQuery->execute(
            $permission,
            (int) $request->input('size', 20),
            (int) $request->input('page', 1),
        );

        return Inertia::render('Catalog/Modification/Index', [
            'modifications' => $modifications,
            'filters' => [],
        ]);
    }

    public function store(Request $request, UserPermission $permission): RedirectResponse
    {
        $dto = ModificationCreateData::validateAndCreate($request->all());
        $modification = $this->createModificationUseCase->execute($dto, $permission);
        return redirect()->route('admin.catalog.modification.show', $modification->id)->with('success', 'Модификация создана');
    }

    public function search_create(Request $request): JsonResponse
    {
        $query = $request->string('search')->trim()->value();

        return response()->json(
            $this->searchModificationCreateQuery->execute($query)
        );
    }

    public function search_product(Request $request, Modification $modification): JsonResponse
    {
        $query = $request->string('search')->trim()->value();

        /** @var array<int, int> $variants attribute_id => variant_id */
        $variants = $request->input('variants', []);

        return response()->json(
            $this->searchModificationProductQuery->execute($modification->id, $query, $variants)
        );
    }

    public function show(Modification $modification, UserPermission $permission): Response
    {
        return Inertia::render('Catalog/Modification/Show', [
            'modification' => $this->viewModificationQuery->execute($modification->id, $permission),
        ]);
    }

    public function rename(Request $request, int $id, UserPermission $permission): RedirectResponse
    {
        $dto = ModificationRenameData::validateAndCreate($request->all());
        $this->renameModificationUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Modification $modification): RedirectResponse
    {
        $this->service->delete($modification);
        return redirect()->back()->with('success', 'Модификация удалена');
    }

    public function setPrimary(Request $request, Modification $modification, UserPermission $permission): RedirectResponse
    {
        $this->setPrimaryModificationProductUseCase->execute(
            $modification->id,
            $request->integer('product_id'),
            $permission,
        );
        return redirect()->back()->with('success', 'Базовый товар изменён');
    }

    public function del_product(Request $request, Modification $modification, UserPermission $permission): RedirectResponse
    {
        $this->removeProductFromModificationUseCase->execute(
            $modification->id,
            $request->integer('product_id'),
            $permission,
        );
        return redirect()->back()->with('success', 'Товар убран из модификации');
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
                    $other = route('admin.catalog.modification.show', $product->modification);
                } else {
                    $other = $product->modification->id;
                }
                $result[] = array_merge($product->toArrayForSearch(), ['other' => $other]);
            }
        }
        return \response()->json($result);
    }

    public function add_product(Request $request, Modification $modification, UserPermission $permission): RedirectResponse
    {
        try {
            $this->addProductToModificationUseCase->execute(
                $modification->id,
                $request->integer('product_id'),
                $permission,
            );
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException|\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
