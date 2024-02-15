<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\ThrowableHasAppeared;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Shop\Parser\HttpPage;
use Illuminate\Console\Command;

class ReserveCommand extends Command
{
    protected $signature = 'cron:reserve';
    protected $description = 'Снятие с резерва';

    public function handle()
    {
        //TODO Сделать Лог (можно через event(new LogData('Текст')))
        // Сколько товаров было снято с резерва
        $this->info('Резерв - проверка');

        try {
            $reserveService = new ReserveService();
            $reserveService->clearByTimer();
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }
}
