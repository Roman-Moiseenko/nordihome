<?php

namespace App\Modules\Shop\Application\Services;


use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;

class RegionalPriceCalculator
{

    public function apply(int $price, ?int $region): int
    {
        if (is_null($region) || $region == 39) return $price;
        //TODO возможно сделать таблицу с коэфициентами по регионам и закешировать при загрузку
        return (int)(ceil($price * 1.4));
    }

    public function productCardData(ProductCardData $data, ?int $region): ProductCardData
    {


        $data->price = $this->apply($data->price, $region);
        $data->price_previous = $this->apply($data->price_previous, $region);
        if ($data->promotion?->price > 0) $data->promotion->price = $this->apply($data->promotion->price, $region);

        return $data;
    }

    public function productData(ProductData $data, ?int $region): ProductData
    {
        $data->price = $this->apply($data->price, $region);
        $data->price_previous = $this->apply($data->price_previous, $region);
        if ($data->promotion?->price > 0) $data->promotion->price = $this->apply($data->promotion->price, $region);

        return $data;
    }
}
