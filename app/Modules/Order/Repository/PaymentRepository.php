<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PaymentRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = OrderPayment::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_begin'))) {
            $filters['date_begin'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_end'))) {
            $filters['date_end'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }

        if ($request->string('user') != '') {
            $user = $request->string('user')->trim()->value();
            $filters['user'] = $user;
            $query->whereHas('order', function ($query) use ($user) {
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('phone', 'LIKE', "%$user%")
                        ->orWhere('email', 'like', "%$user%")
                        ->orWhereRaw("LOWER(fullname) like LOWER('%$user%')");
                });
            });
        }

        if ($request->string('comment') != '') {
            $comment = $request->string('comment')->trim()->value();
            $filters['comment'] = $comment;
            $query->where('document', 'like', "%$comment%");
        }

        if ($request->integer('staff_id') > 0) {
            $staff_id = $request->integer('staff_id');
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }

        if ($request->integer('order') > 0) {
            $order = $request->integer('order');
            $filters['order'] = $order;
            $query->whereHas('order', function ($query) use ($order) {
                $query->where('number', $order);
            });
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(OrderPayment $document) {
                return [
                    'id' => $document->id,
                    'date' => $document->htmlDate(),
                    'amount' => price($document->amount),
                    'order' => $document->order->htmlNumDate(),
                    'user' => $document->order->user->getPublicName(),
                    'document' => $document->document,
                    'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
                    'url' => route('admin.order.payment.edit', $document),
                    'destroy' => route('admin.order.payment.destroy', $document),
                ];
            });
    }


    /**
     * Cписок всех платежных вариантов
     * @return array
     */
    public function getPayments(): array
    {
        //Получаем список всех платежных вариантов
        $payments = PaymentHelper::payments();
        usort($payments, function ($a, $b) {
            return $a['sort'] > $b['sort'];
        });
        return $payments;
    }
}
