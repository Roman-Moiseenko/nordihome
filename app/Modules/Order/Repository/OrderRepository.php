<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;


use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Helpers\OrderHelper;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class OrderRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Order::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }

        if ($request->string('user') != '') {
            $user = $request->string('user')->trim()->value();
            $filters['user'] = $user;
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('phone', 'LIKE', "%$user%")
                    ->orWhere('email', 'like', "%$user%")
                    ->orWhereRaw("LOWER(fullname) like LOWER('%$user%')")
                    ->orWhereHas('shopper', function ($query) use ($user) {
                        $query->where('inn', 'like', "%$user%")
                            ->orWhereRaw("LOWER(short_name) like LOWER('%$user%')");
                    });
            });
        }
        if ($request->string('comment') != '') {
            $comment = $request->string('comment')->trim()->value();
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if ($request->integer('condition') > 0) {
            $condition = $request->integer('condition');
            $filters['condition'] = $condition;
            $query->whereHas('status', function ($q) use ($condition) {
                $q->where('value', $condition);
            });
        }
        if ($request->integer('staff_id') > 0) {
            $staff_id = $request->integer('staff_id');
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Order $order) => $this->OrderToArray($order));
    }

    /*
     [
                'id' => $order->id,
                'opl' => OrderHelper::pictogram($order),
                'otg' => OrderHelper::pictogram($order),
                'number' => $order->htmlNum(),
                'date' => $order->htmlDate(),
                'manager' => is_null($order->manager_id) ? 'Не назначен' : $order->manager->fullname->getShortname(),
                'user' => $order->user->getPublicName(),
                'amount' => price($order->getTotalAmount()),
                'status' => $order->status->value,
                'status_html' => OrderHelper::status($order),
                'has_cancel' => !($order->InWork() || $order->isCanceled() || $order->isCompleted()),
                'url' => route('admin.order.show', $order),

                'canceled' => route('admin.order.canceled', $order),
                'copy' => route('admin.order.copy', $order),
            ]

            if ($order->isAwaiting()) $type = 'warning';
        if ($order->isPaid()) $type = 'green';
        if ($order->isPrepaid()) $type = 'half-green';
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3))) {
                $type = 'red';
                $text = 'Оплата просрочена';
            }
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) {
            $type = 'double-green';
            $text = 'Переплата';
        };

     */

    public function OrderToArray(Order $order): array
    {
        $status_out = -1;
        $status_pay = -1;

        //if ($order->isAwaiting()) $status_pay = -1;
        if ($order->isPrepaid()) $status_pay = 0.5;
        if ($order->isPaid()) $status_pay = 1;
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3))) {
                $status_pay = 0;
            }
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) $status_pay = 2;

        if ($status_pay > -1) $status_out = $order->getPercentIssued();

        return array_merge($order->toArray(), [
            'staff' => is_null($order->staff_id) ? 'Не назначен' : $order->staff->fullname->getShortname(),
            'user' => [
                'name' => $order->user->getPublicName(),
                'phone' => $order->user->phone,
            ],
            'amount' => $order->getTotalAmount(),

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
            'additions' => $order->additions()->get()->map(function (OrderAddition $orderAddition) {
                return array_merge($orderAddition->toArray(), [
                    'calculate' => $orderAddition->getAmount(),
                    'name' => $orderAddition->addition->name,
                    'manual' => $orderAddition->addition->manual,
                    'base' => $orderAddition->addition->base,
                    'is_quantity' => $orderAddition->addition->is_quantity,
                ]);
            }),

            'amount' => [
                'base' => $order->getBaseAmount(),
                'manual' => $order->manual,
                'discount' => $order->discount_amount,
                'total' => $order->getTotalAmount(),
                'addition' => $order->getAdditionsAmount(),
                'promotions' => $order->getDiscountPromotions(),
                'coupon' =>$order->getCoupon(),
                'percent' => ($order->getBaseAmountNotDiscount() == 0) ? 0 : ceil($order->manual / $order->getBaseAmountNotDiscount() * 100 * 10) / 10,
            ],

            'reserve' => $order->getReserveTo(),
        ]);
    }

    private function OrderItemToArray(OrderItem $item): array
    {
        return array_merge($item->toArray(), [
            'percent' => $item->getPercent(),
            'product' => array_merge($item->product->toArray(), [
                'weight' => $item->product->weight(),
                'volume' => ceil($item->product->volume() * 1000) / 1000,
                'measuring' => $item->product->measuring->name,
                'has_promotion' => $item->product->hasPromotion(),
                'quantity_sell' => $item->product->getQuantitySell(),
            ]),
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


}
