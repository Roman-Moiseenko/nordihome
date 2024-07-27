<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Repository;

use App\Modules\Order\Entity\Order\OrderExpense;

class DeliveryRepository
{

    public function getExpense(int $type, string $filter)
    {
        $query = OrderExpense::where('type', $type);
        if ($filter == 'new') $query->where('status', OrderExpense::STATUS_ASSEMBLY);
        if ($filter == 'assembly') $query->where('status', OrderExpense::STATUS_ASSEMBLING);
        if ($filter == 'delivery') $query->where('status', OrderExpense::STATUS_DELIVERY);
        if ($filter == 'completed') $query->where('status', OrderExpense::STATUS_COMPLETED);
        return $query;
    }
}
