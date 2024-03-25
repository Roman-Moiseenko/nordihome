<?php
declare(strict_types=1);

namespace App\Console\Commands\Order;

use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Order\Service\SalesService;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $signature = 'order:clear';

    protected $description = 'Очистка заказов';

    public function handle(): bool
    {
        $orders = Order::get();
        foreach ($orders as $order) {
            try {
                $id = $order->id;
                $order->delete();
                $this->info('Заказ ' . $id . ' удален');
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
        }
        return true;
    }
}
