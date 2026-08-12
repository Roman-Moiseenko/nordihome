<?php

namespace App\Modules\Order\Application\DTOs;

readonly class OrderViewData
{

    public function __construct(
        //Основные данные по заказу

        public int $id,
        public ?int $number,
        public ?int $staffId,
        public int $traderId,

        //Данные о суммах
        public ?float $discountAmount,
        public ?float $couponAmount,
        public float $manual,

        public AmountOrderData $amount,

        //Данные о доставке
        public bool $isPickup,
        public ?string $address,

        public ?string $comment,
        public ?string $commentClient,
        // Данные о клиенте
        public ?ClientOrderData $client,


        /** @var OrderItemViewData[] $items */
        public array $items,
        /** @var OrderItemViewData[] $preOrder */
        public array $preOrder,
        /** @var OrderItemViewData[] $inStock */
        public array $inStock,
        /** @var OrderAdditionViewData[] $additions */
        public array $additions,
        /** @var OrderStatusViewData[] $statuses */
        public array $statuses,
        public string $status,
    )
    {

    }
}
