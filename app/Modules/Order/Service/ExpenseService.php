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
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
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

    public function __construct(
        OrderReserveService $reserveService,
        LoggerService       $logger,
    )
    {
        $this->reserveService = $reserveService;
        $this->logger = $logger;
    }

    public function create(Order $order, Request $request): OrderExpense
    {
        DB::transaction(function () use ($order, $request, &$expense) {
            /** @var Storage $storage */
            $storage = Storage::find($request['storage_id']); //Откуда выдать товар


            $expense = OrderExpense::register($order->id, $storage->id);
            $items = $request['items'];
            foreach ($items as $item) {
                /** @var OrderItem $orderItem */
                $orderItem = OrderItem::find($item['id']);
                $quantity = (float)$item['value']; //Сколько выдать товара
                if ($quantity > 0) {
                    $expense->items()->save(OrderExpenseItem::new($orderItem->id, $quantity));
                    //Проверка на наличие на складе выдачи
                    $reserve = $orderItem->getReserveByStorage($storage->id);
                    if (is_null($reserve) || $reserve->quantity < $quantity)
                        throw new \DomainException('Товара нет в резерве на складе отгрузки, дождитесь исполнения поступления или перемещения!');
                    $this->reserveService->downReserve($orderItem, $quantity); //Убираем из резерва
                    $storageItem = $storage->getItem($orderItem->product_id);
                    $storageItem->sub($quantity); //Списываем со склада

                    $storageItem->refresh();
                    if ($storageItem->quantity < 0) {
                        throw new \DomainException('Исключительная ситуация. Товар есть в резерве, НО требуется перемещение');
                    }
                }
            }
            $expense->storage_id = $storage->id;
            $expense->save();
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

    public function create_expense(Order $order, Request $request): OrderExpense
    {
        $expense = $this->create($order, $request);

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
        if ($method == 'wherehouse') {

        }


        return $expense;
    }

    /**
     * Отмена распоряжения. Возвращаем кол-во в резерв и на склад. Удаляем записи и само распоряжение
     * @param OrderExpense $expense
     * @return Order
     */
    public function cancel(OrderExpense $expense): Order
    {
        DB::transaction(function () use($expense, &$order) {
            $order = $expense->order;
            foreach ($expense->items as $item) {
                $expense->storage->add($item->orderItem->product, $item->quantity);
                $expense->storage->refresh();
                $this->reserveService->upReserve($item->orderItem, $item->quantity);
                $item->delete();
            }
            $this->logger->logOrder($expense->order, 'Отмена распоряжения на выдачу', '', $expense->htmlNumDate());
            $expense->delete();
            $order->refresh();
        });

        return $order;
    }

    public function update_comment(OrderExpense $expense, string $comment)
    {
        $expense->comment = $comment;
        $expense->save();
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

    public function assembly(OrderExpense $expense): Order
    {
        if (!$expense->isStorage()) {
            if ($expense->isLocal() == false && $expense->isRegion() == false) throw new \DomainException('Не выбран тип доставки');
            if ($expense->isLocal() && is_null($expense->calendar())) throw new \DomainException('Не выбрано время доставки');
            if (empty($expense->phone) || empty($expense->address->address)) throw new \DomainException('Не указан адрес и/или телефон');
        }
        $expense->setNumber();
        $expense->assembly();
        event(new ExpenseHasAssembly($expense)); //Уведомление на склад на выдачу
        $this->logger->logOrder($expense->order, 'Распоряжение отправлено на сборку', '', $expense->htmlNumDate());
        return $expense->order;
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
        $this->logger->logOrder($expense->order, 'Распоряжение в пути', '', !empty($track) ? ('Трек посылки ' . $track) : '' );
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
                $this->logger->logOrder($expense->order, 'Товар выдан по распоряжению', '', $expense->htmlNumDate() );
            }
        });
    }
}
