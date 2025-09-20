<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\BannerWidget;
use App\Modules\Page\Entity\BannerWidgetItem;
use App\Modules\Page\Entity\WidgetItem;
use DB;
use Illuminate\Http\Request;

class BannerWidgetService extends WidgetService
{

    public function create(Request $request): BannerWidget
    {
        return BannerWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(BannerWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);
    }

    public function addItem(BannerWidget $widget, Request $request): void
    {
        $file = $request->file('file');
        if (empty($file)) throw new \DomainException('Нет изображения');
        DB::transaction(function () use ($widget, $file) {
            $item = BannerWidgetItem::register($widget->id);
            $item->saveImage($file);
            $item->refresh();
            $item->image->thumb = false;
            $item->image->save();
        });
    }

    public function delItem(BannerWidgetItem|WidgetItem $item): void
    {
        $item->image->delete();
        parent::delItem($item);
    }

    public function setItem(BannerWidgetItem $item, Request $request): void
    {
        $item->saveImage($request->file('file'), $request->boolean('clear_file'));

        $item->slug = $request->string('slug')->trim()->value();

        $item->url = $request->string('url')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
    }

}
