<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Entity\Addition;

class GetAssemblageAdditionUseCase
{

    //FIXME Переделать на AdditionEntity
    public function execute():? Addition
    {
        $addition = Addition::where('slug', 'assembly_15')->first();

        return $addition;
    }
}
