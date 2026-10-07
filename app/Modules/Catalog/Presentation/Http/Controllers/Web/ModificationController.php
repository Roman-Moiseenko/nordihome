<?php

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Modification\AddProductToModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\CreateModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\IndexModificationQuery;
use App\Modules\Catalog\Application\Actions\Modification\RemoveModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\RemoveProductFromModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\RenameModificationUseCase;
use App\Modules\Catalog\Application\Actions\Modification\SearchModificationCreateQuery;
use App\Modules\Catalog\Application\Actions\Modification\SearchModificationProductQuery;
use App\Modules\Catalog\Application\Actions\Modification\SetPrimaryModificationProductUseCase;
use App\Modules\Catalog\Application\Actions\Modification\ViewModificationQuery;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationCreateData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationRenameData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModificationController extends Controller
{
    public function __construct(
        private readonly CreateModificationUseCase $createModificationUseCase,
        private readonly IndexModificationQuery    $indexModificationQuery,
        private readonly SearchModificationCreateQuery $searchModificationCreateQuery,
        private readonly SearchModificationProductQuery $searchModificationProductQuery,
        private readonly AddProductToModificationUseCase $addProductToModificationUseCase,
        private readonly RemoveProductFromModificationUseCase $removeProductFromModificationUseCase,
        private readonly SetPrimaryModificationProductUseCase $setPrimaryModificationProductUseCase,
        private readonly RenameModificationUseCase $renameModificationUseCase,
        private readonly RemoveModificationUseCase $removeModificationUseCase,
        private readonly ViewModificationQuery     $viewModificationQuery,
    )
    {
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

    public function search_product(Request $request, int $id): JsonResponse
    {
        $query = $request->string('search')->trim()->value();

        /** @var array<int, int> $variants attribute_id => variant_id */
        $variants = $request->input('variants', []);

        return response()->json(
            $this->searchModificationProductQuery->execute($id, $query, $variants)
        );
    }

    public function show(int $id, UserPermission $permission): Response
    {
        return Inertia::render('Catalog/Modification/Show', [
            'modification' => $this->viewModificationQuery->execute($id, $permission),
        ]);
    }

    public function rename(Request $request, int $id, UserPermission $permission): RedirectResponse
    {
        $dto = ModificationRenameData::validateAndCreate($request->all());
        $this->renameModificationUseCase->execute($id, $dto, $permission);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $permission): RedirectResponse
    {
        $this->removeModificationUseCase->execute($id, $permission);
        return redirect()->back()->with('success', 'Модификация удалена');
    }

    public function setPrimary(Request $request, int $id, UserPermission $permission): RedirectResponse
    {
        $this->setPrimaryModificationProductUseCase->execute(
            $id,
            $request->integer('product_id'),
            $permission,
        );
        return redirect()->back()->with('success', 'Базовый товар изменён');
    }

    public function del_product(Request $request, int $id, UserPermission $permission): RedirectResponse
    {
        $this->removeProductFromModificationUseCase->execute(
            $id,
            $request->integer('product_id'),
            $permission,
        );
        return redirect()->back()->with('success', 'Товар убран из модификации');
    }


    public function add_product(Request $request, int $id, UserPermission $permission): RedirectResponse
    {
        try {
            $this->addProductToModificationUseCase->execute(
                $id,
                $request->integer('product_id'),
                $permission,
            );
            return redirect()->back()->with('success', 'Товар добавлен');
        } catch (\DomainException|\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
