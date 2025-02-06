<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Base\Traits\FiltersRepository;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PaymentRepository
{
    use FiltersRepository;

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = OrderPayment::orderByDesc('created_at');
        $filters = [];
        $this->_date_from($request, $filters, $query);
        $this->_date_to($request, $filters, $query);


        if ($request->string('user') != '') {
            $user = $request->string('user')->trim()->value();
            $filters['user'] = $user;
            $query->whereHas('order', function ($query) use ($user) {
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('phone', 'LIKE', "%$user%")
                        ->orWhere('email', 'like', "%$user%")
                        ->orWhereRaw("LOWER(fullname) like LOWER('%$user%')")
                        ->orWhereHas('organizations', function ($query) use ($user) {
                            $query->whereRaw("LOWER(full_name) like LOWER('%$user%')")
                                ->orWhere('inn', 'like', "%$user%");
                        });
                });
            });
        }
        $this->_comment($request, $filters, $query);
        $this->_staff_id($request, $filters, $query);

        if (($order = $request->string('order')->value()) != '') {
            $filters['order'] = $order;
            $query->whereHas('order', function ($query) use ($order) {
                $query->where('number', $order);
            });
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(OrderPayment $payment) => $this->PaymentToArray($payment));
    }

    private function PaymentToArray(OrderPayment $payment): array
    {
        return array_merge($payment->toArray(),
            [
                'date' => $payment->htmlDate(),
                'order' => $payment->order,
                'user_name' => is_null($payment->order) ? $payment->shopper->short_name : $payment->order->user->getPublicName(),
                'comment' => $payment->comment,
                'staff' => !is_null($payment->staff) ? $payment->staff->fullname->getFullName() : '-',
                'method_text' => $payment->methodText(),
            ]
        );
    }

    public function PaymentWithToArray(OrderPayment $payment): array
    {
        return array_merge($this->PaymentToArray($payment), [
            'is_cash' => $payment->isCash(),
            'is_card' => $payment->isCard(),
            'is_account' => $payment->isAccount(),
            'refund' => $payment->refund,
        ]);
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
