<?php
declare(strict_types=1);

namespace App\Modules\Shop\Calculate;

use App\Modules\Order\Entity\ItemInterface;

class CalculatorOrder
{

    public function calculate(array $items): array
    {
        $_items = [];
        //TODO Вычесляем общую сумму корзины базовую

        /** @var ItemInterface $item */
        foreach ($items as $item) {
            $product = $item->getProduct();
            $sellCost = $product->lastPrice->value;
            $discount = '';
            //Есть ли акции на товар

            //Есть ли бонусы от продажи Бонусного товара

            //Есть ли бонусы от кол-ва в корзине данного товара

            //Если нет скидок, проверяем есть ли общая скидка
            $_item = clone $item;
            $_item->setSellCost($sellCost);
            $_item->setDiscount($discount);
            $_items[] = $_item;

        }
        return $_items;
    }
}
