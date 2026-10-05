<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Actions\Equivalent\CreateEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\Equivalent\IndexEquivalentQuery;
use App\Modules\Catalog\Application\Actions\Equivalent\RemoveEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\Equivalent\UpdateEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\Equivalent\ViewEquivalentQuery;
use App\Modules\Catalog\Application\Actions\EquivalentProduct\AssignProductsToEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\EquivalentProduct\AttachProductsToEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\EquivalentProduct\DetachProductsFromEquivalentUseCase;
use App\Modules\Catalog\Application\Actions\EquivalentProduct\ListProductByEquivalentUseCase;
use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentCreateData;
use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentUpdateData;
use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentViewData;
use App\Modules\Catalog\Application\DTOs\Equivalent\FilterEquivalentIndexData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EquivalentController extends Controller
{
    public function __construct(
        private readonly IndexEquivalentQuery $indexEquivalentQuery,
        private readonly CreateEquivalentUseCase $createEquivalentUseCase,
        private readonly ViewEquivalentQuery $viewEquivalentQuery,
        private readonly UpdateEquivalentUseCase $updateEquivalentUseCase,
        private readonly RemoveEquivalentUseCase $removeEquivalentUseCase,
        private readonly ListProductByEquivalentUseCase $listProductByEquivalentUseCase,
        private readonly AttachProductsToEquivalentUseCase $attachProductsToEquivalentUseCase,
        private readonly DetachProductsFromEquivalentUseCase $detachProductsFromEquivalentUseCase,
        private readonly AssignProductsToEquivalentUseCase $assignProductsToEquivalentUseCase,
    )
    {
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filters = FilterEquivalentIndexData::validateAndCreate($request->all());
        $equivalents = $this->indexEquivalentQuery->execute($filters, $userPermission);

        return Inertia::render('Catalog/Equivalent/Index', [
            'equivalents' => $equivalents,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = EquivalentCreateData::validateAndCreate($request->all());
        $equivalent = $this->createEquivalentUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.catalog.equivalent.show', $equivalent->id)->with('success', 'Группа создана');
    }

    public function show(int $id, UserPermission $userPermission): Response
    {
        $equivalent = $this->viewEquivalentQuery->execute($id, $userPermission);

        return Inertia::render('Catalog/Equivalent/Show', [
            'equivalent' => EquivalentViewData::fromEntity($equivalent),
        ]);
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = EquivalentUpdateData::validateAndCreate($request->all());
        $equivalent = $this->updateEquivalentUseCase->execute($id, $dto, $userPermission);

        return redirect()->route('admin.catalog.equivalent.show', $equivalent->id)->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeEquivalentUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Группа удалена');
    }

    /**
     * Список товаров группы аналогов (для TableRelation).
     * GET /admin/catalog/equivalent/{id}/products
     */
    public function products(int $id, Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 15);

        $list = $this->listProductByEquivalentUseCase->execute($id, $perPage, $page);

        return response()->json($list, SymfonyResponse::HTTP_OK);
    }

    /**
     * Назначить товары группе аналогов (sync — заменяет весь набор).
     * POST /admin/catalog/equivalent/{id}/products/sync
     */
    public function assignProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->assignProductsToEquivalentUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары назначены'], SymfonyResponse::HTTP_OK);
    }

    /**
     * Добавить товары к группе аналогов (attach — дополняет существующие).
     * POST /admin/catalog/equivalent/{id}/products/attach
     */
    public function attachProducts(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $productIds = [];

        if ($request->has('product_id')) {
            $productIds[] = $request->integer('product_id');
        } else {
            $data = $request->input('products', []);
            if (count($data) === 0) {
                throw new \DomainException('Нет данных');
            }

            if (is_array($data[0])) {
                foreach ($data as $item) {
                    $productIds[] = $item['product_id'];
                }
            } else {
                $productIds = $data;
            }
        }

        $this->attachProductsToEquivalentUseCase->execute($id, $productIds, $userPermission);

        return redirect()->back()->with('success', 'Товары добавлены');
    }

    /**
     * Отвязать товары от группы аналогов.
     * DELETE /admin/catalog/equivalent/{id}/products/detach
     */
    public function detachProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $productIds = $request->input('products', []);

        $this->detachProductsFromEquivalentUseCase->execute($id, $productIds, $userPermission);

        return response()->json(['message' => 'Товары откреплены'], SymfonyResponse::HTTP_OK);
    }
}
