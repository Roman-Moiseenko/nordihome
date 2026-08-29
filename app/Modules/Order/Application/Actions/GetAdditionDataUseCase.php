<?php

namespace App\Modules\Order\Application\Actions;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\DTOs\AdditionData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;

class GetAdditionDataUseCase
{

    //Переделать на AdditionEntity
    public function execute(int $id): AdditionData
    {

        /** @var Addition $additionModel */
        $additionModel = Addition::find($id);

        return new AdditionData(
            baseRatio: $additionModel->base,
            name: $additionModel->name,
            isQuantity: $additionModel->is_quantity,
            isManual: $additionModel->manual,
            calculate: $additionModel->class,
            type: $additionModel->type,
        );
    }
}
