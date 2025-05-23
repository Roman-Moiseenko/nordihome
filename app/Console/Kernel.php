<?php

namespace App\Console;

use App\Modules\Analytics\Entity\LoggerCron;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
       // $logger = LoggerCron::new('Запущен Schedule');
        //Проверка акций
        $schedule->command('cron:promotion')->dailyAt('00:01');
        //Резерв товара - закончился
        $schedule->command('cron:reserve')->everyFiveMinutes();
        //Резерв товара - за 1 день или 12 часов
        $schedule->command('cron:reserve-before')->everyFiveMinutes();
        //Парсим цены
        $schedule->command('cron:parser-price')->dailyAt('01:01');//dailyAt('02:01');
        //Кешируем данные (категории)
        $schedule->command('cron:cache')->dailyAt('04:01');//dailyAt('02:01');
        //Удаляем просроченные токены
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
        //Рассылка по новым товара
        $schedule->command('cron:product-new')->weeklyOn(3, '1:00');
        //Курс валют по ЦБ России
        $schedule->command('cron:currency')->dailyAt('00:02');
        //$schedule->command('cron:currency')->dailyAt('00:01');
        //Проверка доставок ТК
        $schedule->command('cron:delivery')->everySixHours();
        //Проверка оплаты ЮКассы
        $schedule->command('cron:yookassa')->everyFiveMinutes();

        //Команды для ИКЕА
        if (env('SHOP_THEME') == 'nordihome') {
            $schedule->command('furniture')->dailyAt('00:05');
        }
        //Команды для NB
        if (env('SHOP_THEME') == 'nbrussia') {
            //
        }
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
