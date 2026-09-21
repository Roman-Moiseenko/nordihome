<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Queries\ECommerce;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Output\Application\DTOs\ECommerce\ECommerceData;
use App\Modules\Output\Application\DTOs\ECommerce\ECommerceEventData;
use App\Modules\Output\Application\DTOs\ECommerce\ECommerceItemData;

readonly class GetECommerceQuery
{
    public function execute(ECommerceEventData $data): ECommerceData
    {
        return new ECommerceData(
            currencyCode: 'RUB',
            type: $data->e_type,
            items: $this->collectItems($data->e_id, $data->quantity ?? 0),
        );
    }

    /**
     * @return ECommerceItemData[]
     */
    private function collectItems(mixed $id, int $quantity): array
    {
        // Нормализуем входные данные к виду [productId => quantity]
        $cart = [];
        if (is_array($id)) {
            foreach ($id as $item) {
                $cart[(int) $item['id']] = (int) $item['quantity'];
            }
        } elseif ($id !== null && $id !== '') {
            $cart[(int) $id] = $quantity;
        }

        if ($cart === []) {
            return [];
        }

        // Один запрос с eager loading связанных brand/category — без N+1.
        $products = Product::query()
            ->with(['brand', 'category'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($cart as $productId => $qty) {
            /** @var Product|null $product */
            $product = $products->get($productId);
            if ($product === null) {
                continue;
            }

            $items[] = ECommerceItemData::from([
                'id' => $product->code,
                'name' => $product->name,
                'price' => $product->getPrice(),
                'brand' => $product->brand->name,
                'category' => $product->category->getParentNames(),
                'quantity' => $qty,
            ]);
        }

        return $items;
    }
}
