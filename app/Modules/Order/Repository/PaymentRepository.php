<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;

class PaymentRepository
{
    public function getIndex(array $filters)
    {
        //Фильтры по $request
        $query = OrderPayment::orderByDesc('created_at');
        $user_field = $filters['user'];
        $order = $filters['order'];
        $staff = $filters['staff_id'];

        if (!is_null($user_field)) {

            $users = User::where('phone', 'like', "%$user_field%")
                ->orWhere('email', 'like', "%$user_field%")
                ->orWhere('fullname', 'like', "%$user_field%")
                ->pluck('id')->toArray();
            $query->whereHas('order', function ($q) use ($users) {
                $q->whereIn('user_id', $users);
            });
        }


        if (!is_null($order)) $query->whereHas('order', function ($q) use($order) {
            $q->where('number', $order);
        });

        if (!is_null($staff)) $query->where('staff_id', $staff);


        return $query;
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
