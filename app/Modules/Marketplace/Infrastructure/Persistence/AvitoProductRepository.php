<?php

namespace App\Modules\Marketplace\Infrastructure\Persistence;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\FilterAvitoProductIndexData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Infrastructure\Models\AvitoProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class AvitoProductRepository implements AvitoProductRepositoryInterface
{
    public function getById(int $id): AvitoProductEntity
    {
        $model = AvitoProduct::findOrFail($id);

        return $this->hydrate($model);
    }

    public function save(AvitoProductEntity $product): AvitoProductEntity
    {
        $model = $product->id
            ? AvitoProduct::findOrFail($product->id)
            : new AvitoProduct();

        $model->avito_id = $product->avitoId;
        $model->product_id = $product->productId;
        $model->avito_category_id = $product->avitoCategoryId;
        $model->name = $product->name;
        $model->description = $product->description;
        $model->price = $product->price;
        $model->price_package = $product->pricePackage;
        $model->tn_ved = $product->tnVed;
        $model->okpd2 = $product->okpd2;
        $model->delivery = $product->delivery;
        $model->active = $product->active;

        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        AvitoProduct::findOrFail($id)->delete();
    }

    public function getFilteredPaginated(FilterAvitoProductIndexData &$filter): LengthAwarePaginator
    {
        $query = AvitoProduct::query()->orderByDesc('id');

        $filter->count = 0;

        // По умолчанию показываем только активные, 'archive' — только архивные.
        if ($filter->show === 'archive') {
            $query->where('active', false);
            $filter->count++;
        } else {
            $query->where('active', true);
        }

        if ($filter->delivery === 'with') {
            $query->where('delivery', true);
            $filter->count++;
        } elseif ($filter->delivery === 'without') {
            $query->where('delivery', false);
            $filter->count++;
        }

        if (!is_null($filter->search) && trim($filter->search) !== '') {
            $search = trim($filter->search);
            $query->where(function ($q) use ($search) {
                $q->where('avito_products.name', 'like', "%$search%")
                    ->orWhere('avito_products.description', 'like', "%$search%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('code', 'like', "%$search%")
                            ->orWhere('code_search', 'like', "%$search%");
                    });
            });
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->withQueryString()
            ->through(fn(AvitoProduct $model) => $this->hydrate($model));
    }

    public function countActive(): int
    {
        return AvitoProduct::where('active', true)->count();
    }

    public function countArchived(): int
    {
        return AvitoProduct::where('active', false)->count();
    }

    private function hydrate(AvitoProduct $model): AvitoProductEntity
    {
        $entity = new AvitoProductEntity(
            name: $model->name,
            productId: $model->product_id,
            avitoCategoryId: $model->avito_category_id,
        );

        $entity->id = $model->id;
        $entity->avitoId = $model->avito_id;
        $entity->description = $model->description;
        $entity->price = $model->price ?? 0;
        $entity->pricePackage = $model->price_package ?? 0;
        $entity->tnVed = $model->tn_ved;
        $entity->okpd2 = $model->okpd2;
        $entity->delivery = (bool) $model->delivery;
        $entity->active = (bool) $model->active;

        return $entity;
    }
}
