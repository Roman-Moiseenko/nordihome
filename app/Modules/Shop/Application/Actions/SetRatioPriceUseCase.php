<?php

namespace App\Modules\Shop\Application\Actions;

class SetRatioPriceUseCase
{
    private const array RATIO = [
        'ikea' => 1.2,
        'ru' => 1.4,
    ];
    public function __construct()
    {
        //TODO Загрузить данные из настроек
    }

    public function execute(float $price, string $param): float
    {
        return $price * self::RATIO[$param];
    }
}
