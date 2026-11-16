<?php

namespace App\Modules\Marketplace\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Marketplace\Application\Actions\Avito\ImportAvitoCategoryStructureUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SetAvitoCategoryFieldTypeUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SetAvitoCategoryFieldVariantsUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoriesUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\SyncAvitoCategoryFieldsUseCase;
use App\Modules\Marketplace\Application\Actions\Avito\TreeAvitoCategoryQuery;
use App\Modules\Marketplace\Application\Actions\AvitoCommonImage\CreateAvitoCommonImageUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoCommonImage\DeleteAvitoCommonImageUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoCommonImage\ListAvitoCommonImageQuery;
use App\Modules\Marketplace\Application\Actions\AvitoCommonImage\SortAvitoCommonImageUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoCommonImage\UpdateAvitoCommonImageUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoParameter\ListAvitoParameterQuery;
use App\Modules\Marketplace\Application\Actions\AvitoParameter\UpdateAvitoParameterUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry\ImportAvitoParameterRegistryUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry\ListAvitoParameterRegistryQuery;
use App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry\UpdateAvitoParameterRegistryUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\AttachProductToAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\CopyProductPhotosToAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\CreateAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\DetachProductFromAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\IndexAvitoProductQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\ListAvitoProductValuesQuery;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\SetAvitoProductActiveUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\SetAvitoProductCategoryUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\ToggleAvitoProductDeliveryUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\UpdateAvitoProductDescriptionUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\UpdateAvitoProductUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\UpdateAvitoProductValuesUseCase;
use App\Modules\Marketplace\Application\Actions\AvitoProduct\ViewAvitoProductQuery;
use App\Modules\Marketplace\Application\DTOs\AvitoParameter\AvitoParameterUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoParameter\AvitoParameterViewData;
use App\Modules\Marketplace\Application\DTOs\AvitoParameterRegistry\AvitoParameterRegistryUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoParameterRegistry\AvitoParameterRegistryViewData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductAttachData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCategoryData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductCreateData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductDescriptionData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductValueViewData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductValuesUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageCreateData;
use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageSortData;
use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageUpdateData;
use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageViewData;
use App\Modules\Marketplace\Application\Mappers\AvitoProductFields;
use App\Modules\Marketplace\Domain\Exceptions\AvitoFieldNotInRegistryException;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
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
        private readonly ViewAvitoProductQuery $viewAvitoProductQuery,
        private readonly ListAvitoProductValuesQuery $listAvitoProductValuesQuery,
        private readonly UpdateAvitoProductUseCase $updateAvitoProductUseCase,
        private readonly UpdateAvitoProductValuesUseCase $updateAvitoProductValuesUseCase,
        private readonly UpdateAvitoProductDescriptionUseCase $updateAvitoProductDescriptionUseCase,
        private readonly SetAvitoProductCategoryUseCase $setAvitoProductCategoryUseCase,
        private readonly AttachProductToAvitoProductUseCase $attachProductToAvitoProductUseCase,
        private readonly DetachProductFromAvitoProductUseCase $detachProductFromAvitoProductUseCase,
        private readonly CopyProductPhotosToAvitoProductUseCase $copyProductPhotosToAvitoProductUseCase,
        private readonly ToggleAvitoProductDeliveryUseCase $toggleAvitoProductDeliveryUseCase,
        private readonly SetAvitoProductActiveUseCase $setAvitoProductActiveUseCase,
        private readonly SetAvitoCategoryFieldTypeUseCase $setAvitoCategoryFieldTypeUseCase,
        private readonly SetAvitoCategoryFieldVariantsUseCase $setAvitoCategoryFieldVariantsUseCase,
        private readonly ListAvitoParameterQuery $listAvitoParameterQuery,
        private readonly UpdateAvitoParameterUseCase $updateAvitoParameterUseCase,
        private readonly ImportAvitoCategoryStructureUseCase $importAvitoCategoryStructureUseCase,
        private readonly ListAvitoParameterRegistryQuery $listAvitoParameterRegistryQuery,
        private readonly UpdateAvitoParameterRegistryUseCase $updateAvitoParameterRegistryUseCase,
        private readonly ImportAvitoParameterRegistryUseCase $importAvitoParameterRegistryUseCase,
        private readonly ListAvitoCommonImageQuery $listAvitoCommonImageQuery,
        private readonly CreateAvitoCommonImageUseCase $createAvitoCommonImageUseCase,
        private readonly UpdateAvitoCommonImageUseCase $updateAvitoCommonImageUseCase,
        private readonly SortAvitoCommonImageUseCase $sortAvitoCommonImageUseCase,
        private readonly DeleteAvitoCommonImageUseCase $deleteAvitoCommonImageUseCase,
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

        return Inertia::render('Marketplace/Avito/CommonParameters', [
            'parameters' => AvitoParameterViewData::collect($parameters),
        ]);
    }

    public function commonImages(UserPermission $userPermission): Response
    {
        $images = $this->listAvitoCommonImageQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/CommonImages', [
            'images' => AvitoCommonImageViewData::collect($images),
        ]);
    }

    public function storeCommonImage(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoCommonImageCreateData::validateAndCreate($request->all());
        $this->createAvitoCommonImageUseCase->execute($dto, $userPermission);

        return redirect()->back()->with('success', 'Изображение добавлено');
    }

    public function updateCommonImage(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoCommonImageUpdateData::validateAndCreate([
            ...$request->all(),
            'id' => $id,
        ]);
        $this->updateAvitoCommonImageUseCase->execute($dto, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function sortCommonImages(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoCommonImageSortData::validateAndCreate($request->all());
        $this->sortAvitoCommonImageUseCase->execute($dto, $userPermission);

        return redirect()->back()->with('success', 'Порядок сохранён');
    }

    public function deleteCommonImage(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->deleteAvitoCommonImageUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Изображение удалено');
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

    public function registry(UserPermission $userPermission): Response
    {
        $entries = $this->listAvitoParameterRegistryQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/Registry', [
            'registry' => AvitoParameterRegistryViewData::collect($entries),
            'productFields' => AvitoProductFields::options(),
            'kinds' => [
                ['value' => 'product', 'label' => 'Поле товара (AvitoProduct)'],
                ['value' => 'common', 'label' => 'Общий параметр (AvitoParameter)'],
                ['value' => 'skip', 'label' => 'Не используется'],
                ['value' => 'category', 'label' => 'Параметр категории'],
            ],
        ]);
    }

    /**
     * Загрузка русского и английского шаблонов в реестр параметров.
     */
    public function importRegistry(Request $request, UserPermission $userPermission): RedirectResponse
    {
        if (!$userPermission->can('marketplace.avito.edit')) {
            abort(403);
        }

        $request->validate([
            'file_ru' => ['required', 'file', 'mimes:xlsx'],
            'file_en' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $ruPath = $request->file('file_ru')->store('avito');
        $enPath = $request->file('file_en')->store('avito');

        try {
            $created = $this->importAvitoParameterRegistryUseCase->execute(
                storage_path('app/' . $ruPath),
                storage_path('app/' . $enPath),
                $userPermission,
            );
        } finally {
            @unlink(storage_path('app/' . $ruPath));
            @unlink(storage_path('app/' . $enPath));
        }

        return redirect()->back()->with('success', "Добавлено записей в реестр: {$created}");
    }

    /**
     * Сохранение вида (kind) и имени поля сущности для записи реестра.
     */
    public function updateRegistryEntry(int $id, Request $request, UserPermission $userPermission): JsonResponse
    {
        $dto = AvitoParameterRegistryUpdateData::validateAndCreate([
            ...$request->all(),
            'id' => $id,
        ]);

        $entity = $this->updateAvitoParameterRegistryUseCase->execute($dto, $userPermission);

        return response()->json([
            'id' => $entity->id,
            'kind' => $entity->kind,
            'field' => $entity->field,
        ]);
    }

    public function productShow(int $id, UserPermission $userPermission): Response
    {
        $product = $this->viewAvitoProductQuery->execute($id, $userPermission);
        $values = $this->listAvitoProductValuesQuery->execute($id, $userPermission);
        $categories = $this->treeAvitoCategoryQuery->execute($userPermission);

        return Inertia::render('Marketplace/Avito/Show', [
            'product' => $product,
            'values' => AvitoProductValueViewData::collect($values),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $data = $request->all();
        if (!isset($data['productId']) && isset($data['product_id'])) {
            $data['productId'] = $data['product_id'];
        }
        if (!isset($data['productId'])) {
            $data['productId'] = null;
        }

        $dto = AvitoProductCreateData::validateAndCreate($data);
        $product = $this->createAvitoProductUseCase->execute($dto, $userPermission);

        return redirect()->route('admin.marketplace.avito.product.show', $product->id)
            ->with('success', 'Товар добавлен в Авито');
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoProductUpdateData::validateAndCreate($request->all());
        $this->updateAvitoProductUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function updateValues(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoProductValuesUpdateData::validateAndCreate($request->all());
        $this->updateAvitoProductValuesUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Параметры сохранены');
    }

    public function updateDescription(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoProductDescriptionData::validateAndCreate($request->all());
        $this->updateAvitoProductDescriptionUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Описание сохранено');
    }

    public function setCategory(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoProductCategoryData::validateAndCreate($request->all());
        $this->setAvitoProductCategoryUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Категория сохранена');
    }

    public function attachProduct(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = AvitoProductAttachData::validateAndCreate($request->all());
        $this->attachProductToAvitoProductUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Товар связан');
    }

    public function detachProduct(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->detachProductFromAvitoProductUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Связь с товаром удалена');
    }

    public function copyPhoto(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $photoIds = $request->input('photoIds', $request->input('photoId', []));
        $photoIds = is_array($photoIds) ? $photoIds : [$photoIds];

        $this->copyProductPhotosToAvitoProductUseCase->execute($id, $photoIds, $userPermission);

        return redirect()->back()->with('success', 'Изображение скопировано');
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
     * Загрузка структуры категории из английского xlsx-шаблона.
     * Каждый столбец сверяется с реестром параметров.
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
        $fullPath = storage_path('app/' . $path);

        try {
            $this->importAvitoCategoryStructureUseCase->execute($fullPath, $userPermission);
        } catch (AvitoFieldNotInRegistryException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } finally {
            @unlink($fullPath);
        }

        return redirect()->back()->with('success', 'Структура категории загружена');
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
