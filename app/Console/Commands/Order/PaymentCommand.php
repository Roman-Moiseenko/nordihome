<?php
declare(strict_types=1);

namespace App\Console\Commands\Order;

use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentOrder;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Order\Service\SalesService;
use Illuminate\Console\Command;

class PaymentCommand extends Command
{
    protected $signature = 'order:payment {payment_id}';

    protected $description = 'Платеж оплачен';

    public function handle(): bool
    {
        $service = new SalesService(new ReserveService(), new MovementService(new StorageService()));

        $payment_id = $this->argument('payment_id');
        try {
            $payment = PaymentOrder::find($payment_id);
            $service->paidPayment($payment,'Оплата через Консоль');
        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }
        $this->info('Платеж оплачен');
        return true;
    }
}
