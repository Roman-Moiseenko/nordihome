<?php

namespace App\Modules\Order\Application\Actions;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\DTOs\AdditionData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;

class GetAdditionDataUseCase
{

    public function execute(int $id, OrderEntity $orderEntity): AdditionData
    {

        /** @var Addition $additionModel */
        $additionModel = Addition::find($id);


        if (is_null($additionModel->class)) {
            $calculate = null;
        } else {
            $calculate = $additionModel->class::calculateEntity($orderEntity, $additionModel->base);
        }
        return new AdditionData(
            baseRatio: $additionModel->base,
            name: $additionModel->name,
            isQuantity: $additionModel->is_quantity,
            isManual: $additionModel->manual,
            calculate: $calculate,
            type: $additionModel->type,
        );
    }
}
