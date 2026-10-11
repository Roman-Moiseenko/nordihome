<?php

namespace App\Modules\Auth\Domain\ValueObjects;

class WorkerPosition
{
    const string DRIVER = 'driver'; //Доставщик, Водитель
    const string ASSEMBLE = 'assemble'; //Сборка мебели
    const string LOADER = 'loader'; //Грузчик, Упаковка
    const string LOGISTIC = 'logistic';
    const array WORKS = [
        self::LOADER => 'Упаковка на складе',
        self::DRIVER => 'Доставка',
        self::ASSEMBLE => 'Сборка мебели',
        self::LOGISTIC => 'Логист',
    ];
}
