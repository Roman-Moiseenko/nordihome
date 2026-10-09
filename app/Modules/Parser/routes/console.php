<?php

use Illuminate\Console\Scheduling\Schedule as ScheduleDays;

//Обновляем данные по товарам уже спарсенным
//Цену и доступность
Schedule::command('ikea:products-update')
    ->days([ScheduleDays::MONDAY, ScheduleDays::FRIDAY])
    ->at('02:00');
//Кол-во на складах и доступность
Schedule::command('ikea:products-store')
    ->days([ScheduleDays::WEDNESDAY, ScheduleDays::SATURDAY])
    ->at('02:00');
//Все товары из всех разрешенных каталогов
Schedule::command('ikea:products')
    ->days([ScheduleDays::SUNDAY])
    ->at('02:00');
