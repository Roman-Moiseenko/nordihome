<?php

namespace App\Modules\Cart\Application\Actions;

use App\Modules\Cart\Application\DTOs\CartInfoData;
use App\Modules\Cart\Application\DTOs\CartItemData;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class GetCartUseCase
{

    const DELIVERY_PERIOD = [
        ['min' => 0, 'max' => 5, 'value' => 180, 'slug' => 'parser_delivery_0'],
        ['min' => 5, 'max' => 10, 'value' => 160, 'slug' => 'parser_delivery_1'],
        ['min' => 10, 'max' => 15, 'value' => 140, 'slug' => 'parser_delivery_2'],
        ['min' => 15, 'max' => 30, 'value' => 105, 'slug' => 'parser_delivery_3'],
        ['min' => 30, 'max' => 40, 'value' => 85, 'slug' => 'parser_delivery_4'],
        ['min' => 40, 'max' => 50, 'value' => 75, 'slug' => 'parser_delivery_5'],
        ['min' => 50, 'max' => 200, 'value' => 70, 'slug' => 'parser_delivery_6'],
        ['min' => 200, 'max' => 300, 'value' => 68, 'slug' => 'parser_delivery_7'],
        ['min' => 300, 'max' => 400, 'value' => 66, 'slug' => 'parser_delivery_8'],
        ['min' => 400, 'max' => 600, 'value' => 63, 'slug' => 'parser_delivery_9'],
        ['min' => 600, 'max' => 9999999, 'value' => 60, 'slug' => 'parser_delivery_10'],
    ];
    public function __construct(private HybridStorage $storage, private Settings $settings)
    {

    }

    /**
     * @throws BindingResolutionException
     */
    public function execute(): CartInfoData
    {
        $parser = $this->settings->getParser();


        $ratio =$parser->parser_coefficient;
        $sanctioned = $parser->cost_sanctioned;
        $cartItems = $this->storage->load();
        $items = [];
        $amount = 0;
        $discount = 0;
        $quantity = 0;

        $amountCheck = 0;
        $discountCheck = 0;
        $quantityCheck = 0;
        $weight = 0; $fragile = 0;
        /** @var CartItem $item */

        foreach ($cartItems as $item) {

            if ($item->is_parser) {
                $url = route('shop.ikea.product', $item->getProduct()->parser->code);
                $price = $item->base_cost * (1 + (int)$item->product->parser->sanctioned * $sanctioned / 100) * $ratio;
            } else {
                $url = route('shop.product.view', $item->getProduct()->slug);
                if (!is_null($item->product->promotion())) {
                    $item->discount_cost = $item->product->promotion()->pivot->price;
                    $item->discount_name = $item->product->promotion()->title;
                    $item->discount_id = $item->product->promotion()->id;
                }

                $price = $item->base_cost; // empty($item->discount_cost) ? $item->base_cost : $item->discount_cost;
            }

            $itemData = new CartItemData(
                id: $item->id,
                name: $item->product->name,
                image: $item->product->getImage('mini'),
                url: $url,
                isParser: $item->is_parser,
                productId: $item->product->id,
                cost: $price * $item->getQuantity(),
                price: $price,
                quantity: $item->getQuantity(),
                discountId: $item->discount_id ?? null,
                discountPrice: empty($item->discount_cost) ? null : $item->discount_cost * $item->getQuantity(),
                discountName: $item->discount_name,
                check: $item->check,
            );
            $amount += $itemData->cost;
            if ($itemData->discountPrice > 0) $discount += ($itemData->cost - $itemData->discountPrice);
            $quantity += $itemData->quantity;
            if ($itemData->check) {
                $amountCheck += $itemData->cost;
                if ($itemData->discountPrice > 0) $discountCheck += ($itemData->cost - $itemData->discountPrice);
                $quantityCheck += $itemData->quantity;

                if ($itemData->isParser) {
                    $parser = $item->product->parser;
                    $weightParser = $parser->getFullPackWeight();
                    $weight += $weightParser * $itemData->quantity;
                    if ($parser->fragile) $fragile += $weightParser * $itemData->quantity;
                }
            }

            $items[] = $itemData;
        };
        $deliveryParser = $this->getCostDelivery($weight, $fragile);

        return new CartInfoData(
            items: $items,
            amount: $amount,
            discount: $discount,
            quantity: $quantity,
            amountCheck: $amountCheck,
            discountCheck: $discountCheck,
            quantityCheck: $quantityCheck,
            delivery: 0,
            deliveryParser: $deliveryParser,
        );
    }


    private function getCostDelivery(float $weight, float $fragile): float
    {
        if ($weight == 0) return 0;
        $parser = $this->settings->getParser();

        $cost = 0;
        foreach (self::DELIVERY_PERIOD as $item) {
            if ($item['min'] < $weight & $weight <= $item['max']) {
                $slug = $item['slug'];
                $cost = $parser->$slug;
                break;
            }
        }

        $amount = $weight * $cost + $fragile * $parser->cost_weight_fragile;
        return max($parser->parser_delivery, $amount);
    }
}
