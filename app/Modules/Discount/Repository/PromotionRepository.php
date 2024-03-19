<?php
declare(strict_types=1);

namespace App\Modules\Discount\Repository;

use App\Modules\Discount\Entity\Promotion;

class PromotionRepository
{

    public function getIndex()
    {

        return Promotion::orderBy('finish_at', 'DESC')->orderBy('start_at');

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
