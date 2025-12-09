<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Widgets\BannerWidget;
use App\Modules\Page\Entity\Widgets\BannerWidgetItem;
use Illuminate\Http\Request;

class BannerWidgetRepository
{
    public function getIndex(Request $request)
    {
        return BannerWidget::orderBy('name')->get()->map(fn(BannerWidget $widget) => array_merge($widget->toArray(), [
            'count' => $widget->items()->count(),
        ]));
    }

    public function BannerWithToArray(BannerWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'items' => $widget->items()->get()->map(fn(BannerWidgetItem $item) => array_merge($item->toArray(), [
                'image_file' => $item->getImage(),
            ])),
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
        ]);
    }


}
