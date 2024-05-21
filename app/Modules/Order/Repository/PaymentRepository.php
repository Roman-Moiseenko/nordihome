<?php
declare(strict_types=1);

namespace App\Modules\Order\Repository;

use App\Modules\Order\Entity\Order\OrderPayment;
use Illuminate\Http\Request;

class PaymentRepository
{
    public function getIndex(Request $request)
    {
        //Фильтры по $request
        return OrderPayment::orderByDesc('created_at');
    }
}
