<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Entity\Addition;

class GetPackingAdditionUseCase
{

    //FIXME Переделать на AdditionEntity
    public function execute():? Addition
    {
        $addition = Addition::where('slug', 'packing')->first();

        return $addition;
    }
}
