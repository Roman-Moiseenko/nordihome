<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\ReserveHasTimeOut;
use App\Events\ThrowableHasAppeared;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderHistoryStatus;
use Illuminate\Console\Command;

class ReserveCommand extends Command
{
    protected $signature = 'cron:reserve';
    protected $description = 'Снятие с резерва';

    public function handle()
    {
        $this->info('Резерв - проверка');
        //LoggerCron::new('Старт ' . $this->description);

        try {
            //$reserveService = new OrderReserveService();
            /** @var Order[] $orders */
            $orders = [];

            //$reserves = Reserve::where('reserve_at', '<', now())->where('quantity', '>', 0)->get();
            $reserves = OrderReserve::where('reserve_at', '<', now())->where('reserve_at', '>', now()->subMinutes(9))->where('quantity', '>', 0)->get();

            if ($reserves->count() > 0) {

                /** @var OrderReserve $reserve */
                foreach ($reserves as $reserve) {
                    $order = $reserve->orderItem->order;
                    if ($order->status->value < OrderHistoryStatus::AWAITING) {

                        $reserve->delete();

                        if ($order->checkOutReserve()) {


                            $order->setStatus(OrderHistoryStatus::CANCELLED, 'Закончилось время резерва');
                            event(new OrderHasCanceled($order));
                        }
                    } else {
                        $orders[$order->id] = $order;
                    }
                }
            }

            if (!empty($orders)) {

                foreach ($orders as $order) {
                    event(new ReserveHasTimeOut($order));
                }
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }

}
