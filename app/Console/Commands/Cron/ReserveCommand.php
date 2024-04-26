<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\OrderHasCanceled;
use App\Events\ReserveHasTimeOut;
use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Order\Service\OrderReserveService;
use Illuminate\Console\Command;

class ReserveCommand extends Command
{
    protected $signature = 'cron:reserve';
    protected $description = 'Снятие с резерва';

    public function handle()
    {
        $this->info('Резерв - проверка');

        try {
            $reserveService = new OrderReserveService();
            /** @var Order[] $orders */
            $orders = [];

            //$reserves = Reserve::where('reserve_at', '<', now())->where('quantity', '>', 0)->get();
            $reserves = OrderReserve::where('reserve_at', '<', now())->where('quantity', '>', 0)->get();

            if ($reserves->count() > 0) {
                $logger = LoggerCron::new($this->description);
                /** @var OrderReserve $reserve */
                foreach ($reserves as $reserve) {
                    $order = $reserve->orderItem->order;
                    if ($order->status->value < OrderStatus::AWAITING) {
                        $logger->items()->create([
                            'object' => $reserve->orderItem->product->name,
                            'action' => 'Снято с резерва - ',
                            'value' => $reserve->quantity . ' шт.',
                        ]);
                        $reserve->delete();

                        if ($order->checkOutReserve()) {
                            $logger->items()->create([
                                'object' => $order->htmlDate() . ' ' . $order->htmlNum(),
                                'action' => 'Отменен на сумму',
                                'value' => price($order->total),
                            ]);

                            $order->setStatus(OrderStatus::CANCEL, 'Закончилось время резерва');
                            event(new OrderHasCanceled($order));
                        }
                    } else {
                        $orders[$order->id] = $order;
                    }
                }
            }

            if (!empty($orders)) {
                $logger = LoggerCron::new($this->description);
                foreach ($orders as $order) {
                    event(new ReserveHasTimeOut($order));
                    $logger->items()->create([
                        'object' => $order->htmlDate() . ' ' . $order->htmlNum(),
                        'action' => 'Закончился срок резерва',
                        'value' => '',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }

}
