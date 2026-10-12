<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\SizeGrid\CreateSizeGridUseCase;
use App\Modules\Catalog\Application\Actions\SizeGrid\IndexSizeGridQuery;
use App\Modules\Catalog\Application\Actions\SizeGrid\ListSizeGridDimensionsQuery;
use App\Modules\Catalog\Application\Actions\SizeGrid\RemoveSizeGridUseCase;
use App\Modules\Catalog\Application\Actions\SizeGrid\UpdateSizeGridUseCase;
use App\Modules\Catalog\Application\Actions\SizeGrid\ViewSizeGridQuery;
use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridCreateData;
use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridUpdateData;
use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SizeGridController extends Controller
{
    public function __construct(
        private readonly IndexSizeGridQuery $indexSizeGridQuery,
        private readonly ViewSizeGridQuery $viewSizeGridQuery,
        private readonly CreateSizeGridUseCase $createSizeGridUseCase,
        private readonly UpdateSizeGridUseCase $updateSizeGridUseCase,
        private readonly RemoveSizeGridUseCase $removeSizeGridUseCase,
        private readonly ListSizeGridDimensionsQuery $listSizeGridDimensionsQuery,
    ) {
    }

    public function index(UserPermission $userPermission): Response
    {
        $sizeGrids = $this->indexSizeGridQuery->execute($userPermission);

        return Inertia::render('Catalog/SizeGrid/Index', [
            'sizeGrids' => $sizeGrids,
        ]);

    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = SizeGridCreateData::validateAndCreate($request->all());
        $sizeGrid = $this->createSizeGridUseCase->execute($dto, $userPermission);

        return redirect()
            ->route('admin.catalog.size-grid.show', $sizeGrid->id)
            ->with('success', 'Размерная сетка создана');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $sizeGrid = $this->viewSizeGridQuery->execute($id, $userPermission);

        return Inertia::render('Catalog/SizeGrid/Show', [
            'sizeGrid' => SizeGridViewData::fromEntity($sizeGrid),
        ]);

    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = SizeGridUpdateData::validateAndCreate($request->all());
        $sizeGrid = $this->updateSizeGridUseCase->execute($id, $dto, $userPermission);

        return redirect()
            ->route('admin.catalog.size-grid.show', $sizeGrid->id)
            ->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeSizeGridUseCase->execute($id, $userPermission);

        return redirect()
            ->route('admin.catalog.size-grid.index')
            ->with('success', 'Размерная сетка удалена');
    }

    public function dimensions(): JsonResponse
    {
        return response()->json($this->listSizeGridDimensionsQuery->execute());
    }
}
