<?php

namespace App\Console\Commands\Cron;

use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Bank\Service\YookassaService;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Service\OrderPaymentService;
use App\Modules\Order\Service\OrderService;
use App\Modules\Page\Service\CacheService;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\Parser\Job\ParserAvailablePriceProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

class YookassaCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'cron:yookassa';
    protected $description = 'Проверяем оплату по чекам ЮКассы';

    public function handle(
        YookassaService $yookassa,
        OrderPaymentService $paymentService,
        OrderService $orderService,
    ): void
    {
        $orderPayments = OrderPayment::where('method', OrderPayment::METHOD_YOOKASSA)->where('completed', false)->get();
        /** @var OrderPayment $orderPayment */
        foreach ($orderPayments as $orderPayment) {
            Log::info($orderPayment->yookassa_id);
            $status = $yookassa->checkPayment($orderPayment->yookassa_id);
            if ($status == YookassaService::SUCCESS) {
                Log::info('Платеж завершен');
                $paymentService->completed($orderPayment);
            }
            if ($status == YookassaService::CANCELLED) {
                Log::info('Вышел срок ссылки');
                $orderPayment->delete();
            }
            /*
            else {
                //Проверяем срок
                if ($orderPayment->created_at->gt(now()->subDays(3))) {
                    Log::info('Вышел срок оплаты');
                    if ($yookassa->cancelPayment($orderPayment->yookassa_id)) {//Отмена чека ЮКасса
                        $orderService->cancel($orderPayment->order, 'Вышел срок оплаты');//Отмена Заказа
                        $orderPayment->delete(); //Удаление платежа
                    }
                }
            }
            */
        }
    }

}
