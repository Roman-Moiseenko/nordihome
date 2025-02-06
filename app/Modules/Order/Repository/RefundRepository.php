<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Base\Traits\FiltersRepository;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderExpenseRefundAddition;
use App\Modules\Order\Entity\Order\OrderExpenseRefundItem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class RefundRepository
{
    use FiltersRepository;

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = OrderExpenseRefund::orderByDesc('created_at');
        $filters = [];

        $this->_date_from($request, $filters, $query);
        $this->_date_to($request, $filters, $query);
        $this->_comment($request, $filters, $query);
        $this->_staff_id($request, $filters, $query);

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(OrderExpenseRefund $refund) => $this->RefundToArray($refund));
    }

    private function RefundToArray(OrderExpenseRefund $refund): array
    {
        $status = 0;
        if ($refund->isCompleted()) $status = 0.5;
        if ($refund->isPayment()) $status = 1;

        return array_merge($refund->toArray(), [
            'status' => $status,
            'amount' => $refund->amount(),
            'staff' => $refund->staff,
        ]);
    }

    public function RefundWithToArray(OrderExpenseRefund $refund): array
    {
        return array_merge($this->RefundToArray($refund), [
            'expense' => $refund->expense,
            'items' => $refund->items()->get()->map(function (OrderExpenseRefundItem $item) {
                return array_merge($item->toArray(), [
                    'product' => $item->expenseItem->orderItem->product,
                    'sell_cost' => $item->expenseItem->orderItem->sell_cost,
                ]);
            }),
            'additions' => $refund->additions()->get()->map(function (OrderExpenseRefundAddition $addition) {
                return array_merge($addition->toArray(), [
                    'addition' => $addition->expenseAddition->orderAddition->addition,
                    'amount' => $addition->amount,
                    'quantity' => $addition->expenseAddition->orderAddition->quantity,
                ]);
            }),
        ]);
    }
}
