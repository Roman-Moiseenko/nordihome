<?php

namespace App\Modules\Marketplace\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Application\Actions\Avito\SetAvitoCategoryFieldTypeUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SetAvitoCategoryFieldVariantsUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoriesUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoryFieldsUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\TreeAvitoCategoryQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\CreateAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\IndexAvitoProductQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\SetAvitoProductActiveUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\ToggleAvitoProductDeliveryUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoParameter\ListAvitoParameterQuery;
use App\Modules\Marketplace\Application\Actions\AvitoParameter\UpdateAvitoParameterUseCase;
use App\Modules\Marketplace\Application\DTOs\AvitoParameter\AvitoParameterUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoParameter\AvitoParameterViewData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCreateData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Jobs\ImportAvitoStructureJob;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AvitoController extends Controller
{
    public function __construct(
        private readonly TreeAvitoCategoryQuery $treeAvitoCategoryQuery,
        private readonly SyncAvitoCategoriesUseCase $syncAvitoCategoriesUseCase,
        private readonly SyncAvitoCategoryFieldsUseCase $syncAvitoCategoryFieldsUseCase,
        private readonly IndexAvitoProductQuery $indexAvitoProductQuery,
        private readonly AvitoProductRepositoryInterface $avitoProductRepository,
        private readonly AvitoCategoryFieldRepositoryInterface $avitoCategoryFieldRepository,
        private readonly CreateAvitoProductUseCase $createAvitoProductUseCase,
        private readonly ToggleAvitoProductDeliveryUseCase $toggleAvitoProductDeliveryUseCase,
        private readonly SetAvitoProductActiveUseCase $setAvitoProductActiveUseCase,
        private readonly SetAvitoCategoryFieldTypeUseCase $setAvitoCategoryFieldTypeUseCase,
        private readonly SetAvitoCategoryFieldVariantsUseCase $setAvitoCategoryFieldVariantsUseCase,
        private readonly ListAvitoParameterQuery $listAvitoParameterQuery,
        private readonly UpdateAvitoParameterUseCase $updateAvitoParameterUseCase,
    ) {}

    public function index(Request $request, UserPermission $userPermission): Response
    {
        $filter = FilterAvitoProductIndexData::validateAndCreate($request->all());
        $products = $this->indexAvitoProductQuery->execute($filter, $userPermission);

        $filter->active = $this->avitoProductRepository->countActive();
        $filter->archive = $this->avitoProductRepository->countArchived();

        return Inertia::render('Marketplace/Avito/Index', [
            'products' => $products,
            'filters' => $filter,
        ]);
    }

    public function categories(UserPermission $userPermission): Response
    {
        $categories = $this->treeAvitoCategoryQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/Categories', [
            'categories' => $categories,
        ]);
    }

    public function settings(UserPermission $userPermission): Response
    {
        $parameters = $this->listAvitoParameterQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/Settings', [
            'parameters' => AvitoParameterViewData::collect($parameters),
        ]);
    }

    public function updateSettings(Request $request, UserPermission $userPermission): RedirectResponse
    {
        foreach ($request->input('parameters', []) as $item) {
            $dto = AvitoParameterUpdateData::validateAndCreate($item);
            $this->updateAvitoParameterUseCase->execute($dto, $userPermission);
        }

        return redirect()->back()->with('success', 'Настройки сохранены');
    }

    public function operations(): Response
    {
        return Inertia::render('Marketplace/Avito/Operations');
    }

    public function productShow(int $id, UserPermission $userPermission): Response
    {
        if (!$userPermission->can('marketplace.avito.view')) {
            abort(403);
        }

        return Inertia::render('Marketplace/Avito/Show', [
            'id' => $id,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $data = $request->all();
        if (!isset($data['productId']) && isset($data['product_id'])) {
            $data['productId'] = $data['product_id'];
        }

        $dto = AvitoProductCreateData::validateAndCreate($data);
        $product = $this->createAvitoProductUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.marketplace.avito.product.show', $product->id)
            ->with('success', 'Товар добавлен в Авито');
    }

    public function toggleDelivery(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->toggleAvitoProductDeliveryUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function setActive(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $active = $request->boolean('active');
        $this->setAvitoProductActiveUseCase->execute($id, $active, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function syncCategories(UserPermission $userPermission): RedirectResponse
    {
        $this->syncAvitoCategoriesUseCase->execute($userPermission);

        return redirect()->back()->with('success', 'Категории синхронизированы');
    }

    public function syncFields(UserPermission $userPermission): RedirectResponse
    {
        $this->syncAvitoCategoryFieldsUseCase->execute($userPermission);

        return redirect()->back()->with('success', 'Параметры категорий синхронизированы');
    }

    /**
     * Загрузка структуры категорий и параметров из xlsx-шаблона.
     */
    public function uploadStructure(Request $request, UserPermission $userPermission): RedirectResponse
    {
        if (!$userPermission->can('marketplace.avito.edit')) {
            abort(403);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $path = $request->file('file')->store('avito');

        ImportAvitoStructureJob::dispatch(storage_path('app/' . $path));

        return redirect()->back()->with('success', 'Загрузка структуры запущена');
    }

    /**
     * Параметры выбранной категории (для второй колонки страницы категорий).
     */
    public function categoryFields(int $id, UserPermission $userPermission): JsonResponse
    {
        if (!$userPermission->can('marketplace.avito.view')) {
            abort(403);
        }

        return response()->json($this->avitoCategoryFieldRepository->getByCategoryId($id));
    }

    /**
     * Ручное уточнение типа параметра (string | array).
     */
    public function updateFieldType(int $id, int $fieldId, Request $request, UserPermission $userPermission): JsonResponse
    {
        $type = $request->input('type');
        $type = in_array($type, ['string', 'array'], true) ? $type : null;

        $field = $this->setAvitoCategoryFieldTypeUseCase->execute($fieldId, $type, $userPermission);

        return response()->json([
            'id' => $field->id,
            'type' => $field->type,
        ]);
    }

    /**
     * Сохранение вариантов параметра (для полей типа string | array).
     */
    public function updateFieldVariants(int $id, int $fieldId, Request $request, UserPermission $userPermission): JsonResponse
    {
        $variants = $request->input('variants', []);

        $variants = is_array($variants)
            ? array_values(array_unique(array_map('strval', $variants)))
            : [];

        $variants = array_values(array_filter($variants, fn (string $value) => trim($value) !== ''));

        $field = $this->setAvitoCategoryFieldVariantsUseCase->execute($fieldId, $variants, $userPermission);

        return response()->json([
            'id' => $field->id,
            'allowed_values' => $field->allowedValues ?? [],
        ]);
    }
}
