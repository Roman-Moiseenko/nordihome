<?php

namespace App\Modules\Accounting\Application\Actions\ProductPrice;

class CalculateRetailPriceUseCase
{

    public function execute(float $priceIkea): int
    {
        $ratio = 26; //MAINDO Получить из настроек

        return (int)(ceil($priceIkea * $ratio * 1.45)) ;
    }
}
