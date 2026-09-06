<?php

namespace App\Modules\Order\Application\Actions\AdditionGuide;

use App\Modules\Guide\Infrastructure\Models\Addition;

class GetDeliveryAdditionUseCase
{

    public function execute(int $regionCode): Addition
    {
        if ($regionCode == 39) {
            $addition = Addition::where('slug', 'koenig')->first();
        } else {
            $addition = Addition::where('slug', 'russia')->first();
        }
        return $addition;
    }
}
