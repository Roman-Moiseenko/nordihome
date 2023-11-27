<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Discount\Entity\Promotion;
use App\Modules\Discount\Entity\PromotionGroup;
use App\Modules\Product\Entity\Group;
use Carbon\Carbon;

class GroupRepository
{
    public function getNotInPromotions(Promotion $promotion)
    {
        $promotions_id = Promotion::orderBy('id')
            ->where('finish_at', '>', Carbon::now())
            ->where(function ($query) use ($promotion) {
                $query->where('start_at', '<', $promotion->finish_at->format('Y-m-d'))
                    ->where('finish_at', '>', $promotion->start_at->format('Y-m-d'));
            })
            ->pluck('id')->toArray();

        $groups_id = PromotionGroup::whereIn('promotion_id', $promotions_id)->pluck('group_id')->toArray();

        return Group::orderBy('name')->whereNotIn('id', $groups_id)->get();
    }
}
