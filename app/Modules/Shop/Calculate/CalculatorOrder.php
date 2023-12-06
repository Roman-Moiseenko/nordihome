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

    public function __construct()
    {
    }

    /**
     * @param CartItem[] $items
     * @return CartItem[]
     */
    public function calculate(array $items): array
    {
        $product_ids = array_map(function ($item) {return $item->getProduct()->id;}, $items);

        $promotions = (new PromotionRepository)->getActive();
        foreach ($items as &$item) {

            //Акции по товарам
            /** @var Promotion $promotion */
            foreach ($promotions as $promotion) {
                //throw new \DomainException('*****');
                $prom_disc = $promotion->getDiscount($item->getProduct()->id);
                if (!is_null($prom_disc)) {
                    $item->discount_cost = round($item->base_cost * (100 - $prom_disc) / 100);
                    $item->discount_name = $promotion->title;
                }
            }

            //Проверка на бонусы
            /** @var Bonus $bonus_product */
            $bonus_product = Bonus::where('bonus_id', $item->getProduct()->id)->first();
            if (!empty($bonus_product) && in_array($bonus_product->product_id, $product_ids)) {
                $q_bonus = $item->getQuantity();
                $q_product = $q_bonus;
                $name_base_product = '';
                foreach($items as $_item) {
                    if ($_item->getProduct()->id == $bonus_product->product_id) {
                        $q_product = $_item->getQuantity();
                        $name_base_product = $_item->getProduct()->name;
                    }
                }
                if ($q_bonus <= $q_product) {
                    $item->discount_cost = $bonus_product->discount;
                } else { //если кол-во бонусного больше кол-ва ведущего, рассчитать усредненную цену для бонусного
                    $item->discount_cost = round(($q_product * $bonus_product->discount + ($q_bonus - $q_product) * $item->base_cost) / $q_bonus);
                }
                $item->discount_name = 'Бонусный товар';
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
