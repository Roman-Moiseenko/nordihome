<?php
declare(strict_types=1);

namespace App\Console\Commands\Cron;

use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Delivery\Service\GdeposylkaService;
use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Console\Command;
use Tests\CreatesApplication;

class DeliveryCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:delivery';
    protected $description = 'Проверка доставок ТК';

    public function handle(GdeposylkaService $service, DeliveryService $deliveryService): void
    {

        $expenses = OrderExpense::where('status', OrderExpense::STATUS_DELIVERY)
            ->where('type', OrderExpense::DELIVERY_REGION)
            ->getModels();

        foreach ($expenses as $expense) {
            //TODO проверяем по ТК
            $status = $service->findPackage($expense);
            if (!is_null($status)) $deliveryService->setStatus($expense, $status);
        }
    }
}
