<?php
declare(strict_types=1);

namespace App\Modules\Shop\Calculate;

use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Repository\PromotionRepository;
use App\Modules\Order\Entity\ItemInterface;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\Shop\CartItemInterface;

class CalculatorOrder
{

    /**
     * @param CartItemInterface[] $items
     * @return CartItemInterface[]
     */
    public function calculate(array $items): array
    {
        //TODO Переделать под calculate(array &$items): ?Discount
        // Проверить все используемые ф-ции

        $product_ids = array_map(function ($item) {return ($item->getCheck() && !$item->getPreorder()) ? $item->getProduct()->id : -1;}, $items);
        foreach ($items as &$item) {
            //Проверка на Акции
            if (!$item->getPreorder() && $item->getProduct()->hasPromotion()) {
                $item->setSellCost($item->getProduct()->promotion()->pivot->price);
                $item->setDiscountName($item->getProduct()->promotion()->title);
                $item->setDiscount($item->getProduct()->promotion()->id);
                $item->setDiscountType(Promotion::class); //$item->getProduct()->promotion()::class
            }
            //Проверка на бонусы
            /** @var Bonus $bonus_product */
            $bonus_product = Bonus::where('bonus_id', $item->getProduct()->id)->first();
            if (!$item->getPreorder() && !empty($bonus_product) && in_array($bonus_product->product_id, $product_ids)) {
                $q_bonus = $item->getQuantity();
                $q_product = $q_bonus;
                foreach($items as $_item) {
                    if ($_item->getProduct()->id == $bonus_product->product_id) {
                        $q_product = $_item->getQuantity();
                    }
                }
                if ($q_bonus <= $q_product) {
                    $item->setSellCost($bonus_product->discount);
                } else { //если кол-во бонусного больше кол-ва ведущего, рассчитать усредненную цену для бонусного
                    $item->setSellCost(round(($q_product * $bonus_product->discount + ($q_bonus - $q_product) * $item->getBaseCost()) / $q_bonus));
                }
                $item->setDiscountName('Бонусный товар');
                $item->setDiscount($bonus_product->product_id);
                $item->setDiscountType(Bonus::class);
            }
            //Бонус при объеме
        }

        /*

        $discounts = Discount::where('active', true)->get();
        foreach ($discounts as $discount) {
            $discount->render($items);
        }
*/
        return $items;
    }


    /**
     * @param CartItemInterface[] $items
     * @return Discount|null
     */
    public function discount(array $items): ?Discount
    {
        //TODO продумать - выбрать максимальную скидку
        /** @var Discount[] $discounts */
        $discounts = Discount::where('active', true)->get();
        foreach ($discounts as $discount) {
            if ($discount->render($items, false) != 0) return $discount;
        }
        return null;
    }
}
