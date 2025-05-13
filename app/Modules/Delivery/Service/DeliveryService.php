<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Events\ExpenseHasDelivery;
use App\Events\OrderHasCreated;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Analytics\LoggerService;
use App\Modules\Delivery\Entity\DeliveryCargo;
use App\Modules\Delivery\Entity\Local\Tariff;
use App\Modules\Delivery\Entity\Transport\DeliveryData;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Shop\CartItemInterface;
use App\Modules\User\Entity\UserDelivery;
use Illuminate\Http\Request;

class DeliveryService
{

    private LoggerService $logger;
    private ExpenseService $expenseService;

    public function __construct(LoggerService $logger, ExpenseService $expenseService)
    {
        $this->logger = $logger;
        $this->expenseService = $expenseService;
    }

    public function user(int $user_id): UserDelivery
    {
        if ($user = UserDelivery::where('user_id', $user_id)->first()) return $user;
        return UserDelivery::register($user_id);
    }

    /**
     * @param CartItemInterface[] $items
     */
    //TODO Пересчет от адреса
    public function calculate(int $user_id, array $items): DeliveryData
    {
        $user_delivery = $this->user($user_id);

        if ($user_delivery->isRegion() && !empty($user_delivery->region->address) && !empty($user_delivery->company)) {
            return DeliveryHelper::calculate($user_delivery->company, $items, []);
        }
        if ($user_delivery->isLocal() && !empty($user_delivery->local->address)) {
            //TODO Таблица с местным расчетом стоимости доставки + Вес
            $distance = $this->distance();
            $cost = Tariff::orderBy('distance')->where('distance', '>', $distance)->first();
            return new DeliveryData($cost->tariff, 3);
        }
        return new DeliveryData(0, 0);
    }

    //TODO расчет расстояния
    private function distance()
    {
        //Получаем базовые координаты центра из настроек


        //Получаем координаты точки доставки

        return 22; //расстояние в км
    }


    public function create(OrderExpense $expense, Request $request): void
    {

        if (!$expense->isRegion()) throw new \DomainException('Неверный тип доставки');

        $delivery = DeliveryCargo::new($request->integer('company_id'), $request->string('track')->trim()->value());

        $expense->delivery()->save($delivery);
        event(new ExpenseHasDelivery($expense)); //Уведомляем клиента с трек-номером

        $expense->status = OrderExpense::STATUS_DELIVERY;
        $expense->save();
        $this->logger->logOrder($expense->order, 'Распоряжение в пути',
            '', !empty($track) ? ('Трек посылки ' . $track) : '',
            route('admin.order.expense.show', $expense)
        );

    }

    public function setStatus(OrderExpense $expense, int $status): void
    {
        $expense->delivery->completed_at = now();
        $expense->delivery->status = $status;
        $expense->delivery->save();
        if ($status == DeliveryCargo::STATUS_ISSUED) {
            $this->expenseService->completed($expense);
        }
    }
    public function handle(TelegramHasReceived $event): void
    {

    }
}
