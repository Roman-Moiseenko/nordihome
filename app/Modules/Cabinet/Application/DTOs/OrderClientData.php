<?php

namespace App\Modules\Cabinet\Application\DTOs;

readonly class OrderClientData
{
    public function __construct(
        public int $id,
        public OrderInfoData $info,
        //Дата заказа , номер присвоенный, статус, коментарий
        /** @var OrderInfoItemData[] $items */
        public array $items, //Позиции (товарные данные, кол-во, цена за единицу)
        public array $additions, //Доп.услуги
    )
    {
    }
}
