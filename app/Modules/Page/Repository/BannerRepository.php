<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\BannerItem;
use Illuminate\Http\Request;

class BannerRepository
{
    public function getIndex(Request $request)
    {
        return Banner::orderBy('name')->get()->map(fn(Banner $banner) => array_merge($banner->toArray(), [
            'count' => $banner->items()->count(),
        ]));
    }

    public function BannerWithToArray(Banner $banner): array
    {
        return array_merge($banner->toArray(), [
            'items' => $banner->items()->get()->map(fn(BannerItem $item) => array_merge($item->toArray(), [
                'image_file' => $item->getImage(),
            ])),
        ]);
    }


}
