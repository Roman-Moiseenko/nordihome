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
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
use App\Modules\Base\Entity\FullName;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ExpenseService
{
    private OrderReserveService $reserveService;
    private LoggerService $logger;
    private BatchSaleService $batchSaleService;

    public function __construct(
        OrderReserveService $reserveService,
        LoggerService       $logger,
        BatchSaleService    $batchSaleService,
    )
    {
        $this->reserveService = $reserveService;
        $this->logger = $logger;
        $this->batchSaleService = $batchSaleService;
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
                //TODO Удаление или отмена Выдачи
                // $item->delete();

            }
            foreach ($expense->additions as $addition) {
                $addition->amount = 0;
                $addition->save();
            }

            $this->logger->logOrder($expense->order, 'Отмена распоряжения на выдачу', '', $expense->htmlNumDate());
            $expense->status = OrderExpense::STATUS_CANCELED;
            $expense->save();
            //$expense->delete();
            $order->refresh();
        });

        return $order;
    }

    /**
     * Регистрация выдачи товара на месте (без доставки)
     */

    private function issue(array $request): OrderExpense
    {
        // $expense = $this->create($request);

        /*   $expense->type = OrderExpense::DELIVERY_STORAGE;
           $expense->recipient = clone $expense->order->user->fullname;
           $expense->phone = $expense->order->user->phone;
           $expense->save();
           $expense->setNumber();
           return $expense;*/
    }

    /**
     * Выдать товар из магазина, витрины
     */
    public function issue_shop(array $request): OrderExpense
    {
        /*  DB::transaction(function () use ($request, &$expense) {
               $expense = $this->issue($request);
               $this->completed($expense);
               $expense->refresh();
               $this->logger->logOrder($expense->order, 'Выдать товар с магазина', '', $expense->htmlNumDate());
           });
           return $expense;*/
    }

    /**
     * Выдать товар со склада
     * @param array $request
     * @return OrderExpense
     */
    public function issue_warehouse(array $request): OrderExpense
    {
        $expense = $this->issue($request);
        $this->assembly($expense);
        $expense->refresh();
        $this->logger->logOrder($expense->order, 'Выдать товар со склада', '', $expense->htmlNumDate());
        return $expense;
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
        event(new ExpenseHasAssembly($expense)); //Уведомление на склад на выдачу
        $this->logger->logOrder($expense->order, 'Распоряжение отправлено на сборку', '', $expense->htmlNumDate());
    }

    public function assembling(OrderExpense $expense, int $worker_id)
    {
        $expense->worker_id = $worker_id;
        $expense->status = OrderExpense::STATUS_ASSEMBLING;
        $this->logger->logOrder($expense->order, 'Назначен сборщик распоряжению', $expense->htmlNumDate(), $expense->worker->fullname->getFullName());

        $expense->save();
    }

    public function delivery(OrderExpense $expense, string $track = '')
    {
        if ($expense->isRegion()) {
            if (empty($track)) throw new \DomainException('Не указан трек-номер посылки');
            $expense->track = $track;
            event(new ExpenseHasDelivery($expense)); //Уведомляем клиента с трек-номером
        }
        $expense->status = OrderExpense::STATUS_DELIVERY;
        $expense->save();
        $this->logger->logOrder($expense->order, 'Распоряжение в пути', '', !empty($track) ? ('Трек посылки ' . $track) : '');
    }

    public function completed(OrderExpense $expense)
    {
        DB::transaction(function () use ($expense) {
            $expense->completed();
            $expense->refresh();
            /** @var Order $order */
            $order = Order::find($expense->order_id);

            if (($order->getTotalAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder()) < 1) {
                $check = true;
                foreach ($order->expenses as $_expense) {
                    if (!$_expense->isCompleted()) $check = false;
                }
                //Проверить все ли распоряжения выданы?
                if ($check) $expense->order->setStatus(OrderStatus::COMPLETED);
                event(new OrderHasCompleted($expense->order));
                $this->logger->logOrder($expense->order, 'Заказ завершен', '', '');
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
        $expense->address = $request->string('address')->value();
        $expense->comment = $request->string('comment');
        $expense->type = $request->input('type');
        $expense->save();
    }
}
