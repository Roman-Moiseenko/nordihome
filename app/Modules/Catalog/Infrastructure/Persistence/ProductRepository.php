<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Catalog\Infrastructure\Models\CategoryProduct;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function save(ProductEntity $product): ProductEntity
    {
        $model = $product->id
            ? Product::findOrFail($product->id)
            : new Product();

        $model->name = $product->name;
        $model->name_print = $product->namePrint;
        $model->code = $product->code->getCode();
        $model->code_search = $product->code->getCodeSearch();
        $model->slug = (string)$product->slug;
        $model->old_slug = $product->oldSlug;
        $model->main_category_id = $product->mainCategoryId;
        $model->brand_id = $product->brandId;
        $model->series_id = $product->seriesId;
        $model->description = $product->description;
        $model->short = $product->short;
        $model->comment = $product->comment;
        $model->model = $product->model;
        $model->barcode = $product->barcode;
        $model->frequency = $product->frequency;
        $model->vat_id = $product->vatId;
        $model->country_id = $product->countryId;
        $model->measuring_id = $product->measuringId;
        $model->marking_type_id = $product->markingTypeId;
        $model->published = $product->published;
        $model->pre_order = $product->preOrder;
        $model->delivery = $product->delivery;
        $model->local = $product->local;
        $model->priority = $product->priority;
        $model->not_sale = $product->notSale;
        $model->price_reduced = $product->priceReduced;
        $model->only_on_order = $product->onlyOnOrder;
        $model->fractional = $product->fractional;
        $model->hide_price = $product->hidePrice;
        $model->published_at = $product->publishedAt?->format('Y-m-d H:i:s');

        $model->save();

        if ($product->id === null) {
            $product->id = $model->id;
        }

        return $product;
    }

    public function findByCode(string $code): ?ProductEntity
    {
        $model = Product::where('code', $code)->first();

        if ($model === null) return null;

        return $this->hydrate($model);
    }

    public function getByCode(string $code): ?ProductEntity
    {
        $model = Product::where('code_search', $code)->first();

        if ($model === null) return null;

        return $this->hydrate($model);
    }

    public function getById(int $id): ProductEntity
    {
        $model = Product::findOrFail($id);

        return $this->hydrate($model);
    }


    public function findByMainCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Product::where('main_category_id', $categoryId)
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $model) => $this->hydrate($model));
    }

    public function findAllByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        // ID товаров, привязанных через pivot
        $pivotProductIds = CategoryProduct::where('category_id', $categoryId)
            ->pluck('product_id')
            ->toArray();

        return Product::orderBy('name')
            ->where(function ($query) use ($categoryId, $pivotProductIds) {
                // Основная категория
                $query->where('main_category_id', $categoryId);

                // Или привязанные через pivot
                if (!empty($pivotProductIds)) {
                    $query->orWhereIn('id', $pivotProductIds);
                }
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $model) => $this->hydrate($model));
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) return [];

        return Product::whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')') // сохраняем порядок
            ->get()
            ->map(fn($model) => $this->hydrate($model))
            ->all();
    }

    public function search(string $query, int $limit = 10): array
    {
        $models = Product::orderBy('name')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('code_search', 'LIKE', "%{$query}%")
                    ->orWhere('code', 'LIKE', "%{$query}%")
                    ->orWhereRaw("LOWER(name) LIKE LOWER('%{$query}%')");
            })
            ->take($limit)
            ->get();

        return $models->map(fn(Product $model) => $this->hydrate($model))->all();
    }

    private function hydrate(Product $model): ProductEntity
    {
        $entity = new ProductEntity(
            name: $model->name,
            code: Code::fromDatabase($model->code, $model->code_search),
            slug: new Slug($model->slug),
            mainCategoryId: $model->main_category_id,
            brandId: $model->brand_id,
        );
        $entity->id = $model->id;
        $entity->namePrint = $model->name_print ?? $model->name;
        $entity->oldSlug = $model->old_slug ?? '';
        $entity->seriesId = $model->series_id;
        $entity->description = $model->description ?? '';
        $entity->short = $model->short ?? '';
        $entity->comment = $model->comment ?? '';
        $entity->model = $model->model ?? '';
        $entity->barcode = $model->barcode ?? '';
        $entity->frequency = $model->frequency ?? 105;
        $entity->vatId = $model->vat_id;
        $entity->countryId = $model->country_id;
        $entity->measuringId = $model->measuring_id;
        $entity->markingTypeId = $model->marking_type_id;
        $entity->published = (bool)$model->published;
        $entity->preOrder = (bool)$model->pre_order;
        $entity->delivery = (bool)$model->delivery;
        $entity->local = (bool)$model->local;
        $entity->priority = (bool)$model->priority;
        $entity->notSale = (bool)$model->not_sale;
        $entity->priceReduced = (bool)$model->price_reduced;
        $entity->onlyOnOrder = (bool)$model->only_on_order;
        $entity->fractional = (bool)$model->fractional;
        $entity->hidePrice = (bool)$model->hide_price;

        if ($model->published_at !== null) {
            $entity->publishedAt = \DateTimeImmutable::createFromInterface($model->published_at);
        }

        return $entity;
    }
}
