<?php

namespace App\Modules\Page\Repository;

class PromotionRepository
{

    public function getIndex(\Illuminate\Http\Request $request)
    {
        /*
        return Banner::orderBy('name')->get()->map(fn(Banner $banner) => array_merge($banner->toArray(), [
            'count' => $banner->items()->count(),
        ]));
        */
    }
}
