<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Infrastructure\Models\Addition;

class GetPolandAdditionUseCase
{

    //FIXME Переделать на AdditionEntity
    public function execute(): Addition
    {
        $addition = Addition::where('slug', 'poland')->first();

        return $addition;
    }
}
