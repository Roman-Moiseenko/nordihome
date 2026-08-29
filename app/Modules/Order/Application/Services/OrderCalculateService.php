<?php

namespace App\Modules\Order\Application\Services;

use App\Modules\Discount\Entity\Coupon;
use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;

readonly class OrderCalculateService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private GetAdditionDataUseCase  $getAdditionDataUseCase,
    )
    {
    }

    public function execute(int $orderId): void
    {
        $orderEntity = $this->orderRepository->getById($orderId);

        $manual = 0;
        foreach ($orderEntity->items as $item) {
            if (is_null($item->discountId)) {
                $manual += ($item->baseCost-$item->sellCost) * $item->quantity;
            }
            //TODO Добавить пересчет бонусных товаров,
            // проверка для каждого товара, если он бонусный, ищем в заказе и устанавливаем цену

        }

        $orderEntity->manual = $manual;

        foreach ($orderEntity->additions as $addition) {
            $additionData = $this->getAdditionDataUseCase->execute($addition->additionId);

            //Если не ручное заполнение
            if (!$additionData->isManual) {
                //Есть калькулятор
                if (!is_null($additionData->calculate)) {
                    $addition->amount = $additionData->calculate::calculateEntity($orderEntity, $additionData->baseRatio);
                } else {
                    //Базовое значение стоимости
                    $addition->amount = $additionData->baseRatio;
                }
                //Если параметр количественный
                if ($additionData->isQuantity) {
                    $addition->amount = $addition->amount * $addition->quantity;
                }
            }
        }
        if (!is_null($orderEntity->couponId)) {
            //TODO Сделать через useCase
            $coupon = Coupon::find($orderEntity->couponId);
            $orderEntity->couponAmount = $coupon->bonus;
        }
        $this->orderRepository->save($orderEntity);
    }

}
