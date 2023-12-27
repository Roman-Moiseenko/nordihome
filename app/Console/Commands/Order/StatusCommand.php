<?php
declare(strict_types=1);

namespace App\Console\Commands\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Console\Command;

class StatusCommand extends Command
{
    protected $signature = 'order:status {order_id} {value}';

    protected $description = 'Установить статус';

    public function handle(): bool
    {
        $order_id = $this->argument('order_id');
        $value = $this->argument('value');
        try {
            $order = Order::find($order_id);
            $order->setStatus($value);
        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }
        $this->info('Статус' . OrderStatus::STATUSES[$value] . ' установлен');
        return true;
    }
}
