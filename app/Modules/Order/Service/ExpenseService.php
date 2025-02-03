<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\ExpenseHasAssembly;
use App\Events\ExpenseHasCompleted;
use App\Events\ExpenseHasDelivery;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasCompleted;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\BatchSaleService;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Analytics\LoggerService;
use App\Modules\Base\Entity\FullName;
use App\Modules\Delivery\Entity\DeliveryCargo;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;


class ExpenseService
{
    private OrderReserveService $reserveService;
    private LoggerService $logger;
    private BatchSaleService $batchSaleService;
    private StaffRepository $staffs;

    public function __construct(
        OrderReserveService $reserveService,
        LoggerService       $logger,
        BatchSaleService    $batchSaleService,
        StaffRepository     $staffs,
    )
    {
        $this->reserveService = $reserveService;
        $this->logger = $logger;
        $this->batchSaleService = $batchSaleService;
        $this->staffs = $staffs;
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

            $this->logger->logOrder($expense->order, 'Создано распоряжение на выдачу', '', $expense->htmlNumDate());
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
            $this->logger->logOrder($expense->order, 'Выдать товар с магазина', '', $expense->htmlNumDate());
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
            $this->batchSaleService->return($expense); //Удаляем продажи по партиям
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
            $this->logger->logOrder($expense->order, 'Отмена распоряжения на выдачу', '', $expense->htmlNumDate());
            $expense->status = OrderExpense::STATUS_CANCELED;
            $expense->save();
            $order->refresh();
        });

        return $order;
    }

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
        $this->logger->logOrder($expense->order, 'Распоряжение отправлено на сборку', '', $expense->htmlNumDate());
    }

    public function setLoader(OrderExpense $expense, int $worker_id): void
    {
        $expense->workers()->attach($worker_id, ['work' => Worker::WORK_LOADER]);
        $expense->push();
        $expense->refresh();

        $message = 'Список товаров на сборку';
        $expense->status = OrderExpense::STATUS_ASSEMBLING;
        $this->logger->logOrder(
            $expense->order,
            'Назначен грузчик распоряжению',
            $expense->htmlNumDate(),
            $expense->getWorker(Worker::WORK_LOADER)->fullname->getFullName());
        $expense->save();

        /** @var Worker $worker */
        $worker = Worker::find($worker_id);
        //$staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_DELIVERY);

        //foreach ($staffs as $staff) {
            $worker->notify(new StaffMessage(
                event: NotificationHelper::EVENT_ASSEMBLY,
                message: $message,
                params: new TelegramParams(TelegramParams::OPERATION_ASSEMBLING, $expense->id)
            ));
        //}
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
        /*$workers = $expense->getAssemble();
        foreach ($workers as $worker) {
            $expense->workers()->detach($worker->id);
        }*/
    }


    public function setDriver(OrderExpense $expense, int $worker_id): void
    {
        $expense->workers()->attach($worker_id, ['work' => Worker::WORK_DRIVER]);
        $expense->push();
        $expense->refresh();

        $expense->status = OrderExpense::STATUS_DELIVERY;
        $this->logger->logOrder(
            $expense->order,
            'Назначен доставщик распоряжению',
            $expense->htmlNumDate(),
            $expense->getWorker(Worker::WORK_DRIVER)->fullname->getFullName());
        $expense->save();
    }

    public function setAssemble(OrderExpense $expense, array $workers): void
    {
        foreach ($workers as $worker_id)
            $expense->workers()->attach($worker_id, ['work' => Worker::WORK_ASSEMBLE]);
        $expense->push();
        $expense->refresh();

        $this->logger->logOrder(
            $expense->order,
            'Назначен сборщик мебели',
            $expense->htmlNumDate(),
            $expense->getWorker(Worker::WORK_ASSEMBLE)->fullname->getFullName());
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
            $expense->completed();
            $expense->refresh();

            $order = $expense->order;

            if (($order->getTotalAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder()) < 1) {
                $check = true;
                foreach ($order->expenses as $_expense) {
                    if (!$_expense->isCompleted()) $check = false;
                }
                //Проверить все ли распоряжения выданы?
                if ($check) {
                    $expense->order->setStatus(OrderStatus::COMPLETED);
                    event(new OrderHasCompleted($expense->order));
                    $this->logger->logOrder($expense->order, 'Заказ завершен', '', '');
                } else {
                    event(new ExpenseHasCompleted($expense));
                    $this->logger->logOrder($expense->order, 'Товар выдан по распоряжению', '', $expense->htmlNumDate());
                }
            } else {
                event(new ExpenseHasCompleted($expense));
                $this->logger->logOrder($expense->order, 'Товар выдан по распоряжению', '', $expense->htmlNumDate());
            }
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



}
