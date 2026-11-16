<?php

use Illuminate\Support\Facades\Schedule;

// Обновление ad_number (avito_id) у активных объявлений Авито без номера.
// Запуск 2 раза в месяц: 2-го и 16-го числа в 02:00.
Schedule::command('avito:update-id')
    ->cron('0 2 2,16 * *')
    ->environments('production');
