<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Events\OrderHasCanceled;
use App\Events\ReserveHasTimeOut;
use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Shop\Parser\HttpPage;
use Illuminate\Console\Command;

class ReserveBeforeCommand extends Command
{
    protected $signature = 'cron:reserve-before';
    protected $description = 'Снятие с резерва';
    const BEFORE_TIME_OUT = 12;

    public function handle()
    {
        $this->info('Резерв - проверка');
        /** @var Order[] $orders */
        $orders = [];
        try {
            $reserves = Reserve::where('reserve_at', '<', now()->addHours(self::BEFORE_TIME_OUT))->where('quantity', '>', 0)->get();
            if ($reserves->count() > 0) {

                /** @var Reserve $reserve */
                foreach ($reserves as $reserve) {
                    if ($reserve->type == Reserve::TYPE_ORDER) {
                        $order = $reserve->orderItem->order;
                        $orders[$order->id] = $order;
                    }
                }
            }
            if (!empty($orders)) {
                $logger = LoggerCron::new($this->description);
                foreach ($orders as $order) {
                    event(new ReserveHasTimeOut($order, false));
                    $logger->items()->create([
                        'object' => $order->htmlDate() . ' ' . $order->htmlNum(),
                        'action' => 'Заканчивается срок резерва',
                        'value' => 'Осталось ' . self::BEFORE_TIME_OUT . ' ч',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }

}
