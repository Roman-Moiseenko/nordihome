<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;


use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Base\Traits\FiltersRepository;
use App\Modules\Delivery\Service\CalendarService;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderMovement;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Helpers\OrderHelper;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class OrderRepository
{
    use FiltersRepository;

    private UserRepository $users;
    private CalendarService $calendar;

    public function __construct(UserRepository $users, CalendarService $calendar)
    {
        $this->users = $users;
        $this->calendar = $calendar;
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Order::orderByDesc('created_at');
        $filters = [];

        $this->_date_from($request, $filters, $query);
        $this->_date_to($request, $filters, $query);
        $this->_comment($request, $filters, $query);
        $this->_staff_id($request, $filters, $query);

        if ($request->string('user') != '') {
            $user = $request->string('user')->trim()->value();
            $filters['user'] = $user;
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('phone', 'LIKE', "%$user%")
                    ->orWhere('email', 'like', "%$user%")
                    ->orWhereRaw("LOWER(fullname) like LOWER('%$user%')")
                    ->orWhereHas('organization', function ($query) use ($user) {
                        $query->where('inn', 'like', "%$user%")
                            ->orWhereRaw("LOWER(short_name) like LOWER('%$user%')");
                    });
            });
        }

        if ($request->integer('condition') > 0) {
            $condition = $request->integer('condition');
            $filters['condition'] = $condition;
            $query->whereHas('status', function ($q) use ($condition) {
                $q->where('value', $condition);
            });
        }

        if (count($filters) > 0) $filters['count'] = count($filters);
        /*
        if (!$request->boolean('canceled')) {
            $filters['canceled'] = false;
            $query->whereHas('status', function ($query) {
                $query->whereNotIn('value', [OrderStatus::CANCEL, OrderStatus::CANCEL_BY_CUSTOMER, OrderStatus::REFUND]);
            });
        } else {
            $filters['canceled'] = true;
        }
        if (!$request->boolean('completed')) {
            $filters['completed'] = false;
            $query->whereHas('status', function ($query) {
                $query->whereNotIn('value', [OrderStatus::COMPLETED, OrderStatus::COMPLETED_REFUND]);
            });
        } else {
            $filters['completed'] = true;
        }
        */

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Order $order) => $this->OrderToArray($order));
    }

    public function OrderToArray(Order $order): array
    {
        $status_out = -1;
        $status_pay = -1;

        if ($order->isPrepaid()) $status_pay = 0.5;
        if ($order->isPaid()) $status_pay = 1;
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3))) {
                $status_pay = 0;
            }
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) $status_pay = 2;
        if ($status_pay > -1) $status_out = $order->getPercentIssued();

        if ($order->isCompleted()) {
            $status_out = 1;
            if ($order->getPaymentAmount() > $order->getTotalAmount()) {
                $status_pay = 2;
            } else {
                $status_pay = 1;
            }
        }
        return array_merge($order->toArray(), [
            'staff' => is_null($order->staff_id) ? 'Не назначен' : $order->staff->fullname->getShortname(),
            'user' => [
                'name' => $order->user->getPublicName(),
                'phone' => $order->user->phone,
            ],
            'amount' => $order->getTotalAmount(),
            'refund' => $order->getRefundAmount(),
            'status' => [
                'is_new' => $order->isNew(),
                'is_manager' => $order->isManager(),
                'is_awaiting' => $order->isAwaiting(),
                'is_prepaid' => $order->isPrepaid(),
                'is_paid' => $order->isPaid(),
                'is_completed' => $order->isCompleted(),
                'is_canceled' => $order->isCanceled(),
                'in_work' => $order->inWork(),
            ],

            'status_text' => $order->statusHtml(),
            'has_cancel' => !($order->inWork() || $order->isCanceled() || $order->isCompleted()),
            'status_pay' => $status_pay,
            'status_out' => $status_out,
        ]);
    }

    public function OrderWithToArray(Order $order): array
    {
        return array_merge($this->OrderToArray($order), [
            'in_stock' => $order->items()->where('preorder', false)->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'pre_order' => $order->items()->where('preorder', true)->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'items' => $order->items()->get()->map(fn(OrderItem $item) => $this->OrderItemToArray($item)),
            'additions' => $order->additions()->get()->map(fn(OrderAddition $orderAddition) => $this->OrderAdditionToArray($orderAddition)),
            'user' => is_null($order->user_id) ? null : $this->users->UserToArray($order->user),
            'amount' => [
                'base' => $order->getBaseAmount(),
                'manual' => (int)$order->manual,
                'discount' => $order->discount_amount,
                'total' => $order->getTotalAmount(),
                'addition' => $order->getAdditionsAmount(),
                'promotions' => $order->getDiscountPromotions(),
                'coupon' => $order->getCoupon(),
                'percent' => ($order->getBaseAmountNotDiscount() == 0) ? 0 : ceil($order->manual / $order->getBaseAmountNotDiscount() * 100 * 10) / 10,
                'payment' => $order->getPaymentAmount(),
                'refund' => $order->getRefundAmount(),
            ],
            'emails' => is_null($order->shopper_id) ? [] : array_select($order->shopper->getEmails()),
            'shoppers' => is_null($order->user) ? [] : $order->user->organizations,
            'reserve' => $order->getReserveTo(),
            'payments' => $order->payments()->get()->map(fn(OrderPayment $payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'method_text' => $payment->methodText(),
            ]),
            'movements' => $order->movements()->get()->map(fn(MovementDocument $movement) => [
                'id' => $movement->id,
                'number' => $movement->number,
                'status_text' => $movement->statusHTML(),
            ]),
            'expenses' => $order->expenses()->get()->map(fn(OrderExpense $expense) => [
                'id' => $expense->id,
                'number' => $expense->number,
                'created_at' => $expense->created_at,
                'status_text' => $expense->statusHTML(),
                'is_canceled' => $expense->isCanceled(),
                'is_completed' => $expense->isCompleted(),
            ]),
            'weight' => $order->getWeight(),
            'volume' => $order->getVolume(),
        ]);
    }

    public function OrderLogToArray(Order $order): array
    {
        return array_merge($order->toArray(), [
            'logs' => $order->logs()->with('staff')->get(),
        ]);
    }

    private function OrderItemToArray(OrderItem $item): array
    {
        $quantity_sell = $item->product->getQuantitySell();
        $quantity_order = $item->order->getQuantityProduct($item->product_id, false);
        $refund = $item->getRefund();

        return array_merge($item->toArray(), [
            'percent' => $item->getPercent(),
            'product' => array_merge($item->product->toArray(), [
                'weight' => $item->product->weight(),
                'volume' => ceil($item->product->volume() * 1000) / 1000,
                'measuring' => $item->product->measuring->name,
                'has_promotion' => $item->product->hasPromotion(),
                //'quantity_sell' => $item->product->getQuantitySell(),
            ]),
            'supply_stack' => is_null($item->supply_stack_id) ? null : [
                'id' => $item->supplyStack->id,
                'status_text' => $item->supplyStack->status(),
                'supply_id' => $item->supplyStack->supply_id,
            ],
            'remains' => $item->getRemains(),
            'reserves' => ($item->reserves()->count() == 0) ? null : $item->reserves,
            'storages' => Storage::orderBy('name')->get()->map(function (Storage $storage) use ($item) {

                $storageItem = $storage->getItem($item->product_id);
                $orderReserve = is_null($storageItem) ? null : $item->getReserveByStorageItem($storageItem->id);
                return [
                    'id' => $storage->id,
                    'name' => $storage->name,
                    'reserve' => is_null($orderReserve) ? 0 : (float)$orderReserve->quantity,
                    'quantity' => is_null($storageItem) ? 0 : (float)$storageItem->quantity,
                    'reserve_other' => is_null($storageItem) ? 0 : $storageItem->getQuantityReserve($item->order_id),
                ];
            }),
            'quantity_sell' => $quantity_order + $quantity_sell,
            'refund' => $refund == 0 ? null : $refund,
        ]);
    }

    private function OrderAdditionToArray(OrderAddition $orderAddition): array
    {
        $refund = $orderAddition->getRefund();
        return array_merge($orderAddition->toArray(), [
            'calculate' => $orderAddition->getAmount(),
            'name' => $orderAddition->addition->name,
            'manual' => $orderAddition->addition->manual,
            'base' => $orderAddition->addition->base,
            'is_quantity' => $orderAddition->addition->is_quantity,
            'remains' => $orderAddition->getRemains(),
            'refund' => $refund == 0 ? null : $refund,
        ]);
    }

    /**
     * Возвращает список заказов, которые ждут оплаты, в том числе с внесенной предоплатой
     * @param int|null $order_id - id текущего заказа, для случая, когда надо поменять назначения платежа, для уже оплаченного заказа
     * @return mixed
     */
    public function getNotPaidYet(int $order_id = null): mixed
    {
        $query = Order::whereHas('status', function ($query) {
            $query->where('value', OrderStatus::PREPAID)->orWhere('value', OrderStatus::AWAITING);
        });
        if (!is_null($order_id)) $query->orWhere('id', $order_id);
        return $query->orderBy('number')->get();
    }

    public function guideAddition(): array
    {
        $array = [];
        foreach (Addition::TYPES as $type => $label) {
            $array[] = [
                'label' => $label,
                'additions' => Addition::orderBy('name')->where('type', $type)->getModels(),
            ];

        }
        return $array;
    }

    public function ExpenseWithToArray(OrderExpense $expense): array
    {
        return array_merge($expense->toArray(), [
            'status_text' => $expense->statusHTML(),
            'type_text' => $expense->typeHTML(),
            'items' => $expense->items()->get()->map(fn(OrderExpenseItem $expenseItem) => array_merge($expenseItem->toArray(), [
                'product' => $expenseItem->orderItem->product,
                'quantity' => (float)$expenseItem->quantity,
                'refund' => $expenseItem->quantityRefund(),
                'is_honest' => $expenseItem->orderItem->product->isMarking(),
            ])),
            'additions' => $expense->additions()->get()->map(fn(OrderExpenseAddition $expenseAddition) => array_merge($expenseAddition->toArray(), [
                'addition' => $expenseAddition->orderAddition->addition,
                'refund' => $expenseAddition->amountRefund(),
            ])),
            'status' => [
                'is_new' => $expense->isNew(),
                'is_assembly' => $expense->isAssembly(),
                'is_assembling' => $expense->isAssembling(),
                'is_delivery' => $expense->isDelivery(),
                'is_completed' => $expense->isCompleted(),
                'is_canceled' => $expense->isCanceled(),
            ],
            'weight' => $expense->getWeight(),
            'volume' => $expense->getVolume(),
            'is_delivery' => !$expense->isStorage(),
            'calendar' => is_null($expense->calendarPeriod) ? null : [
                'date_at' => $expense->calendarPeriod->calendar->date_at,
                'period_id' => $expense->calendarPeriod->id,
                'periods' => $this->calendar->getDayPeriods($expense->calendarPeriod->calendar->date_at),
            ],
            'workers' => $expense->workers()->get()->map(function (Worker $worker) {
                return array_merge($worker->toArray(), [
                    'work' => Worker::WORKS[$worker->pivot->work],
                ]);
            }),
            'refunds' => ($expense->refunds()->count() == 0) ? null : true,

        ]);
    }

    /**
     * Заказы которые в работе и есть товары под заказ
     */
    public function getInWorkWithPreorder()
    {
        $query = Order::whereHas('status', function ($query) {
            $query->whereIn('value', [OrderStatus::FORMED, OrderStatus::SET_MANAGER]);
        })->whereHas('items', function ($query) {
            $query->where('preorder', true);
        });

        return $query->getModels();
    }


}
