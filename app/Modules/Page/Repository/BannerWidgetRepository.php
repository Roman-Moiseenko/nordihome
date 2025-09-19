<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\BannerWidgetItem;
use Illuminate\Http\Request;

class BannerWidgetRepository
{
    public function getIndex(Request $request)
    {
        return BannerWidget::orderBy('name')->get()->map(fn(BannerWidget $banner) => array_merge($banner->toArray(), [
            'count' => $banner->items()->count(),
        ]));
    }

    public function BannerWithToArray(BannerWidget $banner): array
    {
        return array_merge($banner->toArray(), [
            'items' => $banner->items()->get()->map(fn(BannerWidgetItem $item) => array_merge($item->toArray(), [
                'image_file' => $item->getImage(),
            ])),
        ]);
    }


}
