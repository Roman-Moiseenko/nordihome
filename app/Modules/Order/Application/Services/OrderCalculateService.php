<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Catalog\Entity\Bonus;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Shop\CartItemInterface;

class OrderCalculateService
{

    public function __construct(
        private CouponService $coupons,
        private OrderRepositoryInterface $orderRepository,
    )
    {

    }

    public function execute(int $orderId): void
    {
        $orderEntity = $this->orderRepository->getById($orderId);

        foreach ($orderEntity->items as $item) {

            //TODO Пересчет для всех позиций
        }


        foreach ($orderEntity->additions as $addition) {
            //TODO Пересчет для всех позиций
        }
        $this->orderRepository->save($orderEntity);
        //Выход =>

        /** @var Order $order */
        $order = Order::find($orderId);
        /** @var OrderItem[] $items */
        $items = $order->items()->getModels();

        //Ищем акционные и бонусные товары
        /** @var OrderItem[] $items */
        $items = $this->calculate($items);
        foreach ($items as $item) {
            $item->save();
        }
        //Общие скидки, если имеется, фиксируем сумму (т.к. %% в будущем может измениться)
        if (!is_null($discount = $this->discount($items))) {
            $order->discount_id = $discount->id;
            $order->discount_amount = (int)ceil($order->getBaseAmount() * $discount->discount / 100);
        } else {
            $order->discount_id = null;
            $order->discount_amount = 0;
        }


        //Ручная скидка от скидок за товары
        $order->manual = 0;
        foreach ($order->items as $item) {
            if (is_null($item->discount_id)) {
                $order->manual += ($item->base_cost - $item->sell_cost) * $item->quantity;
            }
        }

        //Пересчет для купона
        if (!is_null($order->coupon_id)) {
            /** @var Coupon $coupon */
            $coupon = Coupon::find($order->coupon_id);
            $order->coupon_amount = $this->coupons->discount($coupon, $order);
        }
        $order->save();

    }


    private function calculate(array $items): array
    {
        //TODO Переделать под calculate(array &$items): ?Discount
        // Проверить все используемые ф-ции

        $product_ids = array_map(function ($item) {
            return ($item->getCheck() && !$item->getPreorder()) ? $item->getProduct()->id : -1;
        }, $items);
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
                $q_bonus = $item->quantity;
                $q_product = $q_bonus;
                foreach ($items as $_item) {
                    if ($_item->getProduct()->id == $bonus_product->product_id) {
                        $q_product = $_item->quantity;
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
    private function discount(array $items): ?Discount
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
