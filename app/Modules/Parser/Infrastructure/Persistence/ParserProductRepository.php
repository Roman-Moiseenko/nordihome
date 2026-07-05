<?php

declare(strict_types=1);

namespace App\Modules\Parser\Infrastructure\Persistence;

use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\ValueObjects\Composite;
use App\Modules\Parser\Domain\ValueObjects\Package;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class ParserProductRepository implements ParserProductRepositoryInterface
{
    public function getById(int $id): ParserProductEntity
    {
        $model = ParserProduct::findOrFail($id);
        return $this->hydrate($model);
    }

    public function getByCode(string $code): ?ParserProductEntity
    {
        $model = ParserProduct::where('code', $code)->first();

        if ($model === null) {
            return null;
        }
        return $this->hydrate($model);
    }

    public function save(ParserProductEntity $product): ParserProductEntity
    {
        $model = $product->id
            ? ParserProduct::findOrFail($product->id)
            : new ParserProduct();

        $model->name = $product->name;
        $model->code = $product->code;
        $model->slug = (string) $product->slug;
        $model->url = $product->url;
        $model->price_base = $product->priceBase;
        $model->price_sell = $product->priceSell;
        $model->short = $product->short;
        $model->description = $product->description;
        $model->fragile = $product->fragile;
        $model->sanctioned = $product->sanctioned;
        $model->availability = $product->availability;
        $model->product_id = $product->productId;

        // composite — массив Composite[]
        $model->composite = array_map(
            fn(Composite $c) => ['product_id' => $c->getProductId(), 'quantity' => $c->getQuantity()],
            $product->composite
        );

        // packages — массив Package[]
        $model->packages = array_map(
            fn(Package $p) => $p->toArray(),
            $product->packages
        );

        // colors — массив строк
        $model->colors = $product->colors;

        $model->save();

        return $this->hydrate($model);
    }

    public function delete(int $id): void
    {
        $model = ParserProduct::findOrFail($id);
        $model->delete();
    }


    private function hydrate(ParserProduct $model): ParserProductEntity
    {
        $entity = new ParserProductEntity(
            name: $model->name,
            code: $model->code,
        );

        $entity->id = $model->id;
        $entity->productId = $model->product_id;
        $entity->slug = new Slug($model->slug ?? '');
        $entity->url = $model->url ?? '';
        $entity->priceBase = (float) ($model->price_base ?? 0);
        $entity->priceSell = (float) ($model->price_sell ?? 0);
        $entity->short = $model->short ?? '';
        $entity->description = $model->description ?? '';
        $entity->fragile = (bool) ($model->fragile ?? false);
        $entity->sanctioned = (bool) ($model->sanctioned ?? false);
        $entity->availability = (bool) ($model->availability ?? false);

        // composite
        $compositeData = $model->composite ?? [];
        $entity->composite = array_map(
            fn(array $item) => new Composite(
                productId: (int) ($item['product_id'] ?? 0),
                quantity: (int) ($item['quantity'] ?? 1),
            ),
            $compositeData
        );

        // packages
        $packagesData = $model->packages ?? [];
        $entity->packages = array_map(
            fn(array $item) => Package::fromArray($item),
            $packagesData
        );

        // colors
        $entity->colors = $model->colors ?? [];

        return $entity;
    }
}
