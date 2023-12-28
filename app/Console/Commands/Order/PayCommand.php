<?php
declare(strict_types=1);

namespace App\Console\Commands\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Console\Command;

class PayCommand extends Command
{
    protected $signature = 'order:pay {order_id}';

    protected $description = 'Заказ оплачен';



    public function handle(): bool
    {
        $order_id = $this->argument('order_id');
        try {
            $order = Order::find($order_id);

            foreach ($order->items as $item) {
                if ($item->reserve_id != null) {
                    $item->reserve()->delete();
                } else {
                    $order->setStatus(OrderStatus::REFUND);
                    throw new \DomainException('Произведена оплата за отмененный заказ');
                }
            }

            $order->setStatus(OrderStatus::PAID);


        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }
        $this->info('Заказ оплачен');
        return true;
    }
}
