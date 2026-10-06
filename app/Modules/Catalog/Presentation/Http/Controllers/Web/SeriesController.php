<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Series\AttachProductsToSeriesUseCase;
use App\Modules\Catalog\Application\Actions\Series\CreateSeriesUseCase;
use App\Modules\Catalog\Application\Actions\Series\DetachProductFromSeriesUseCase;
use App\Modules\Catalog\Application\Actions\Series\IndexSeriesQuery;
use App\Modules\Catalog\Application\Actions\Series\ListSeriesProductsQuery;
use App\Modules\Catalog\Application\Actions\Series\ListSeriesQuery;
use App\Modules\Catalog\Application\Actions\Series\RemoveSeriesUseCase;
use App\Modules\Catalog\Application\Actions\Series\UpdateSeriesUseCase;
use App\Modules\Catalog\Application\Actions\Series\ViewSeriesQuery;
use App\Modules\Catalog\Application\DTOs\Series\FilterSeriesIndexData;
use App\Modules\Catalog\Application\DTOs\Series\SeriesCreateData;
use App\Modules\Catalog\Application\DTOs\Series\SeriesUpdateData;
use App\Modules\Catalog\Application\DTOs\Series\SeriesViewData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SeriesController extends Controller
{
    public function __construct(
        private readonly ListSeriesQuery                $listSeriesUseCase,
        private readonly IndexSeriesQuery               $indexSeriesQuery,
        private readonly CreateSeriesUseCase            $createSeriesUseCase,
        private readonly ViewSeriesQuery                $viewSeriesQuery,
        private readonly ListSeriesProductsQuery        $listSeriesProductsQuery,
        private readonly UpdateSeriesUseCase            $updateSeriesUseCase,
        private readonly RemoveSeriesUseCase            $removeSeriesUseCase,
        private readonly AttachProductsToSeriesUseCase  $attachProductsToSeriesUseCase,
        private readonly DetachProductFromSeriesUseCase $detachProductFromSeriesUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filters = FilterSeriesIndexData::validateAndCreate($request->all());
        $series = $this->indexSeriesQuery->execute($filters, $userPermission);

        return Inertia::render('Catalog/Series/Index', [
            'series' => $series,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = SeriesCreateData::validateAndCreate($request->all());

        $series = $this->createSeriesUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.series.show', $series->id)->with('success', 'Серия добавлена');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $series = $this->viewSeriesQuery->execute($id, $userPermission);
        $products = $this->listSeriesProductsQuery->execute($id, $userPermission);

        return Inertia::render('Catalog/Series/Show', [
            'series' => SeriesViewData::fromEntity($series, $products),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = SeriesUpdateData::validateAndCreate($request->all());

        $series = $this->updateSeriesUseCase->execute($id, $dto, $userPermission);
        return redirect()->route('admin.catalog.series.show', $series->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeSeriesUseCase->execute($id, $userPermission);
        return redirect()->back()->with('success', 'Серия удалена');
    }

    public function add_product(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $productId = $request->integer('product_id');

        $this->attachProductsToSeriesUseCase->execute($id, [$productId], $userPermission);
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $data = $request->input('products', []);
        $productIds = [];

        foreach ($data as $item) {
            $productIds[] = is_array($item) ? (int)($item['product_id'] ?? 0) : (int)$item;
        }

        $productIds = array_values(array_filter($productIds, fn(int $productId) => $productId > 0));

        $this->attachProductsToSeriesUseCase->execute($id, $productIds, $userPermission);
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function del_product(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $productId = $request->integer('product_id');

        $this->detachProductFromSeriesUseCase->execute($id, $productId, $userPermission);
        return redirect()->back()->with('success', 'Товар удален');
    }

    public function list(): JsonResponse
    {
        $list = $this->listSeriesUseCase->execute();
        return response()->json($list);
    }
}
