<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Modules\Discount\Entity\Promotion;

class PromotionHelper
{
    public static function html(Promotion $promotion): string
    {
        $status = $promotion->status();

        if ($status == Promotion::STATUS_DRAFT) return '<span class="p-1 rounded-lg bg-danger text-white">Черновик</span>';
        if ($status == Promotion::STATUS_WAITING) return '<span class="p-1 rounded-lg bg-warning text-white">В ожидании</span>';
        if ($status == Promotion::STATUS_STARTED) return '<span class="p-1 rounded-lg bg-success text-white">Запущена</span>';
        if ($status == Promotion::STATUS_FINISHED) return '<span class="p-1 rounded-lg bg-slate-100 text-slate-500">Завершена</span>';
        return '';
    }
}
