<?php
declare(strict_types=1);

namespace App\Modules\Shop\Calculate;

use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Order\Entity\ItemInterface;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Shop\Cart\CartItem;

class CalculatorOrder
{

    /**
     * @param CartItem[] $items
     * @return CartItem[]
     */
    public function calculate(array $items): array
    {
        $product_ids = array_map(function ($item) {return ($item->check) ? $item->getProduct()->id : -1;}, $items);
        //Проверка на Акции

        foreach ($items as &$item) {
            if ($item->product->hasPromotion()) {
                $item->discount_cost = $item->product->promotion()->pivot->price;
                $item->discount_name = $item->product->promotion()->title;
                $item->discount_id = $item->product->promotion()->id;
                $item->discount_type = $item->product->promotion()::class;
            }

            //Проверка на бонусы
            /** @var Bonus $bonus_product */
            $bonus_product = Bonus::where('bonus_id', $item->getProduct()->id)->first();
            if (!empty($bonus_product) && in_array($bonus_product->product_id, $product_ids)) {
                $q_bonus = $item->quantity;
                $q_product = $q_bonus;
                foreach($items as $_item) {
                    if ($_item->product->id == $bonus_product->product_id) {
                        $q_product = $_item->quantity;
                    }
                }
                if ($q_bonus <= $q_product) {
                    $item->discount_cost = $bonus_product->discount;
                } else { //если кол-во бонусного больше кол-ва ведущего, рассчитать усредненную цену для бонусного
                    $item->discount_cost = round(($q_product * $bonus_product->discount + ($q_bonus - $q_product) * $item->base_cost) / $q_bonus);
                }
                $item->discount_name = 'Бонусный товар';
                $item->discount_id = $bonus_product->product_id;
                $item->discount_type = Bonus::class;
            }
            //Бонус при объеме
        }

        /** @var Discount[] $discounts */
        $discounts = Discount::where('active', true)->get();
        foreach ($discounts as $discount) {
            $discount->render($items);
        }

        return $items;
    }
}
