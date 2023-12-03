<?php
declare(strict_types=1);

namespace App\Modules\Discount\Repository;

use App\Modules\Discount\Entity\Promotion;

class PromotionRepository
{

    public function getIndex(int $pagination = null)
    {
        if (is_null($pagination)) {
            return Promotion::orderBy('finish_at', 'DESC')->orderBy('start_at');
        } else {
            return Promotion::orderBy('finish_at', 'DESC')->orderBy('start_at')->paginate($pagination);
        }
    }

    public function getActive()
    {
        $promotions = [];
        /** @var Promotion $promotion */
        foreach (Promotion::where('published', true)->get() as $promotion) {
             if ($promotion->status() == Promotion::STATUS_STARTED)
             $promotions[] = $promotion;
         }
        return $promotions;
    }
}
