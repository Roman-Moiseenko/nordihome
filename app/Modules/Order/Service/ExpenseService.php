<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\BatchSaleService;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Analytics\LoggerService;
use App\Modules\Bank\Service\YookassaService;
use App\Modules\Base\Entity\FullName;
use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Notification\Message\StaffMessage;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderExpenseWorker;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Events\ExpenseHasCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ExpenseService
{
    private OrderReserveService $reserveService;
    private LoggerService $logger;
    private BatchSaleService $batchSaleService;
    private StaffRepository $staffs;
    private YookassaService $yookassaService;

    public function __construct(
        OrderReserveService $reserveService,
        LoggerService       $logger,
        BatchSaleService    $batchSaleService,
        StaffRepository     $staffs,
        YookassaService     $yookassaService,
    )
    {
        $this->reserveService = $reserveService;
        $this->logger = $logger;
        $this->batchSaleService = $batchSaleService;
        $this->staffs = $staffs;
        $this->yookassaService = $yookassaService;
    }

    /**
     * Создаем Распоряжение на выдачу
     */
    public function create(Order $order, Storage $storage, Request $request): OrderExpense
    {
        DB::transaction(function () use ($storage, $order, $request, &$expense) {
            $expense = OrderExpense::register($order->id, $storage->id);
            $items = $request['items'];
            foreach ($items as $item) {
                /** @var OrderItem $orderItem */
                $orderItem = OrderItem::find($item['id']);
                $quantity = (float)$item['value']; //Сколько выдать товара

                if ($quantity <= 0) throw new \DomainException('Недопустимое кол-во на выдачу - ' . $quantity);
                //Проверка на наличие на складе выдачи
                $reserve = $orderItem->getReserveByStorage($storage->id);
                if (is_null($reserve) || $reserve->quantity < $quantity)
                    throw new \DomainException('Товара нет в резерве на складе отгрузки, дождитесь исполнения поступления или перемещения!');

                $expense->items()->save(OrderExpenseItem::new($orderItem->id, $quantity));
                $this->reserveService->downReserve($orderItem, $quantity); //Убираем из резерва
                $storageItem = $storage->getItem($orderItem->product_id);
                $storageItem->sub($quantity); //Списываем со склада
                $storageItem->refresh();
                if ($storageItem->quantity < 0)
                    throw new \DomainException('Исключительная ситуация. Товар есть в резерве, НО требуется перемещение');
            }
            $expense->storage_id = $storage->id;
            $expense->save();
            $expense->refresh();
            if ($order->getPaymentAmount() < $order->getExpenseAmount()) throw new \DomainException('Нехватает средств на выдачу товара');

            $this->batchSaleService->create($expense); //Проведение по партиям

            $additions = $request['additions'] ?? [];
            foreach ($additions as $addition) {
                if ((float)$addition['value'] > 0)
                    $expense->additions()->save(OrderExpenseAddition::new((int)$addition['id'], (float)$addition['value']));
            }
            $expense->refresh();

            $this->logger->logOrder($expense->order, 'Создано распоряжение на выдачу',
                '', $expense->htmlNumDate(),
                route('admin.order.expense.show', $expense)
            );
        });

        return $expense;
    }

    /**
     * Распоряжение на выдачу в зависимости от метода выдачи (С магазина, Со склада, На доставку).
     */
    public function create_expense(Order $order, Request $request): OrderExpense
    {
        if (is_null($storage_id = $request->input('storage_id'))) {
            $storage = Storage::default(); //Если склад не выбран (Доставка), то основной
        } else {
            $storage = Storage::find($storage_id);
        }
        $expense = $this->create($order, $storage, $request);
        $method = $request->string('method')->value();


        if ($method == 'expense') {
            $user = $expense->order->user;
            $expense->recipient = clone $user->fullname;
            $expense->address = clone $user->address;
            $expense->phone = $user->phone;
            if (!$user->isStorage()) $expense->type = $user->delivery;
            $expense->save();
            return $expense;
        }

        $expense->type = OrderExpense::DELIVERY_STORAGE;
        $expense->recipient = clone $expense->order->user->fullname;
        $expense->phone = $expense->order->user->phone;
        $expense->save();
        $expense->setNumber();

        if ($method == 'shop') {
            $this->completed($expense);
            $expense->refresh();
            $this->logger->logOrder($expense->order, 'Выдать товар с магазина',
                '', $expense->htmlNumDate(), null);
        }
        if ($method == 'warehouse') {
            //Нет отличных данных
        }
        return $expense;
    }

    /**
     * Отмена распоряжения. Возвращаем кол-во в резерв и на склад. Удаляем записи и само распоряжение
     */
    public function cancel(OrderExpense $expense): Order
    {
        DB::transaction(function () use ($expense, &$order) {
            //TODO Если есть чек Сделать Возврат.


            $this->batchSaleService->returnByExpense($expense); //Удаляем продажи по партиям
            $order = $expense->order;
            foreach ($expense->items as $item) {
                //Возвращаем на склад
                $expense->storage->add($item->orderItem->product, (float)$item->quantity);
                $expense->refresh();
                //Добавляем в резерв
                $this->reserveService->upReserve($item->orderItem, (float)$item->quantity);
                $item->quantity = 0;
                $item->save();
            }
            foreach ($expense->additions as $addition) {
                $addition->amount = 0;
                $addition->save();
            }
            //Удаляем календарь доставки
            if (!is_null($period = $expense->calendarPeriod)) {
                $expense->calendarPeriods()->detach();
                $period->refresh();
                if ($period->expenses()->count() == 0) $period->delete();
            }
            //Удаляем назначенных рабочих
            $expense->workers()->detach();
            $this->logger->logOrder($expense->order, 'Отмена распоряжения на выдачу',
                '', $expense->htmlNumDate(),
                route('admin.order.expense.show', $expense));
            $expense->status = OrderExpense::STATUS_CANCELED;
            $expense->save();
            $order->refresh();
        });

        return $order;
    }

    /** Отправляем распоряжение на сборку */
    public function assembly(OrderExpense $expense): void
    {
        if (!$expense->isStorage()) {
            if (!$expense->isLocal() && !$expense->isRegion()) throw new \DomainException('Не выбран тип доставки');
            if ($expense->isLocal() && is_null($expense->calendar())) throw new \DomainException('Не выбрано время доставки');
            if (empty($expense->phone) || empty($expense->address->address)) throw new \DomainException('Не указан адрес и/или телефон');
        }
        $expense->setNumber();
        $expense->assembly();

        //Уведомление на склад на выдачу
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_DELIVERY);
        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                event: NotificationHelper::EVENT_INFO,
                message: 'Новое распоряжение на сборку',

            ));
        }
        $this->logger->logOrder($expense->order, 'Распоряжение отправлено на сборку',
            '', $expense->htmlNumDate(),
            route('admin.order.expense.show', $expense));
    }

    public function setLoader(OrderExpense $expense, int $worker_id): void
    {
        $expense->workers()->attach($worker_id, ['work' => Worker::WORK_LOADER]);
        $expense->push();
        $expense->refresh();

        $message = 'Список товаров на сборку';
        $expense->status = OrderExpense::STATUS_ASSEMBLING;
        $this->logger->logOrder($expense->order, 'Назначен грузчик распоряжению',
            $expense->htmlNumDate(),
            $expense->getWorker(Worker::WORK_LOADER)->fullname->getFullName(),
            route('admin.order.expense.show', $expense));
        $expense->save();

        /** @var Worker $worker */
        $worker = Worker::find($worker_id);

        $worker->notify(new StaffMessage(
            event: NotificationHelper::EVENT_ASSEMBLY,
            message: $message,
            params: new TelegramParams(TelegramParams::OPERATION_ASSEMBLING, $expense->id)
        ));

    }

    public function delLoader(OrderExpense $expense): void
    {
        $worker_id = $expense->getLoader()->id;

        $expense->status = OrderExpense::STATUS_ASSEMBLY;
        $expense->save();
        /** @var Worker $worker */
        $worker = Worker::find($worker_id);
        $worker->notify(new StaffMessage(
            event: NotificationHelper::EVENT_INFO,
            message: 'Отмена сборки по распоряжению ' . $expense->number,
        ));

        OrderExpenseWorker::where('expense_id', $expense->id)->where('work', Worker::WORK_LOADER)->delete();
    }

    public function delDriver(OrderExpense $expense): void
    {
        $worker_id = $expense->getDriver()->id;
        $expense->status = OrderExpense::STATUS_ASSEMBLED;
        $expense->save();
        /** @var Worker $worker */
        $worker = Worker::find($worker_id);
        $worker->notify(new StaffMessage(
            event: NotificationHelper::EVENT_INFO,
            message: 'Отмена доставки по распоряжению ' . $expense->number,
        ));
        OrderExpenseWorker::where('expense_id', $expense->id)->where('work', Worker::WORK_DRIVER)->delete();

    }

    public function delAssemble(OrderExpense $expense): void
    {
        OrderExpenseWorker::where('expense_id', $expense->id)->where('work', Worker::WORK_ASSEMBLE)->delete();
    }

    public function setDriver(OrderExpense $expense, int $worker_id): void
    {
        $expense->workers()->attach($worker_id, ['work' => Worker::WORK_DRIVER]);
        $expense->push();
        $expense->refresh();

        $expense->status = OrderExpense::STATUS_DELIVERY;
        $this->logger->logOrder($expense->order, 'Назначен доставщик распоряжению',
            $expense->htmlNumDate(),
            $expense->getWorker(Worker::WORK_DRIVER)->fullname->getFullName(),
            route('admin.order.expense.show', $expense));
        $expense->save();
    }

    public function setAssemble(OrderExpense $expense, array $workers): void
    {
        $_workers = [];
        foreach ($workers as $worker_id) {
            $expense->workers()->attach($worker_id, ['work' => Worker::WORK_ASSEMBLE]);
            $_workers[] = Worker::find($worker_id)->fullname->getFullName();
        }
        $expense->push();
        $expense->refresh();

        $this->logger->logOrder($expense->order, 'Назначен сборщик(и) мебели',
            $expense->htmlNumDate(),
            implode(', ', $_workers),
            route('admin.order.expense.show', $expense));
    }

    /**
     * Груз собран - Обрабатываем ответ от Телеграм
     */
    public function handle(TelegramHasReceived $event): void
    {
        if ($event->operation == TelegramParams::OPERATION_ASSEMBLING) {
            $expense = OrderExpense::find($event->id);
            if ($expense->isAssembled()) {
                $event->user->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_ERROR,
                        'Вы уже отметили!'
                    )
                );
            } else {
                $this->assembled($expense);
                $event->user->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_INFO,
                        'Принято!'
                    )
                );
            }
        }
    }

    /**
     * Груз собран - ручное назначение статуса
     */
    public function assembled(OrderExpense $expense): void
    {
        $expense->status = OrderExpense::STATUS_ASSEMBLED;
        $expense->save();
    }

    public function completed(OrderExpense $expense): void
    {

        DB::transaction(function () use ($expense) {
            $expense->status = OrderExpense::STATUS_COMPLETED;
            $expense->save();
            $expense->refresh();

            $order = $expense->order;
            $order->refresh();
            //Создание чеков если есть продажи по ЮКассе
            foreach ($order->payments as $payment) {
                if ($payment->isYooKassa()) {
                    $this->yookassaService->createReceipt($expense, $payment);
                    break;
                }
            }

            if (($order->getTotalAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder()) < 1) {
                $check = true;
                foreach ($order->expenses as $_expense) {
                    if (!$_expense->isCompleted() && !$_expense->isCanceled()) $check = false;
                }

                //Проверить все ли распоряжения выданы?
                if ($check) {
                    $expense->order->setStatus(OrderStatus::COMPLETED);
                    $this->logger->logOrder($expense->order, 'Заказ завершен',
                        '', '', null);
                } else {
                    $this->logger->logOrder($expense->order, 'Товар выдан по распоряжению',
                        '', $expense->htmlNumDate(),
                        route('admin.order.expense.show', $expense));
                }
            } else {
                $this->logger->logOrder($expense->order, 'Товар выдан по распоряжению',
                    '', $expense->htmlNumDate(),
                    route('admin.order.expense.show', $expense));
            }
            event(new ExpenseHasCompleted($expense));
        });
    }

    public function setInfo(OrderExpense $expense, Request $request): void
    {
        $expense->recipient = FullName::create(params: $request->input('recipient'));
        $expense->phone = phoneToDB($request->string('phone')->value());
        $expense->address->address = $request->string('address')->value();
        $expense->comment = $request->string('comment');
        if ($expense->isLocal() && $request->input('type') !== OrderExpense::DELIVERY_LOCAL) {
            $expense->calendarPeriods()->detach();
        }

        $expense->type = $request->input('type');
        $expense->save();
    }

    public function setHonest(OrderExpense $expense, Request $request): void
    {
        //Log::info(json_encode($request->all()));
        $signs = $request->input('signs');
        foreach ($expense->items as $item) {
            foreach ($signs as $sign) {
                if ($sign['id'] === $item->id) {
                    $array = explode("\n", $sign['signs']);
                    $item->honest_signs = $array;
                    $item->save();
                }
            }
        }
    }


}
