<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Banner;
use App\Modules\Page\Entity\BannerItem;
use DB;
use Illuminate\Http\Request;

class BannerService
{

    public function create(Request $request): Banner
    {
        return Banner::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setBanner(Banner $banner, Request $request): void
    {
        $banner->name = $request->string('name')->trim()->value();
        $banner->template = $request->string('template')->trim()->value();
        $banner->caption = $request->string('caption')->trim()->value();
        $banner->description = $request->string('description')->trim()->value();
        $banner->name = $request->string('name')->trim()->value();
        $banner->save();
    }

    public function delBanner(Banner $banner): void
    {
        if ($banner->active) throw new \DomainException('Нельзя удалить активный баннер');
        foreach ($banner->items as $item) {
            $this->delItem($item);
        }
        $banner->delete();
    }

    public function addItem(Banner $banner, Request $request): void
    {
        $file = $request->file('file');
        if (empty($file)) throw new \DomainException('Нет изображения');
        DB::transaction(function () use ($banner, $file) {
            $item = BannerItem::register($banner->id);
            $item->saveImage($file);
            $item->refresh();
            $item->image->thumb = false;
            $item->image->save();
        });
    }

    public function delItem(BannerItem $item): void
    {
        $item->image->delete();
        $item->delete();
        //Пересортировка
        foreach ($item->banner->items as $i => $_item) {
            $_item->sort = $i;
            $_item->save();
        }
    }

    public function setItem(BannerItem $item, Request $request): void
    {
        $item->saveImage($request->file('file'), $request->boolean('clear_file'));

        $item->slug = $request->string('slug')->trim()->value();

        $item->url = $request->string('url')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
    }

    public function toggle(Banner $banner): void
    {
        if (!$banner->active && $banner->items()->count() == 0) throw new \DomainException('Нет элементов для показа');
        $banner->active = !$banner->active;
        $banner->save();
    }

    public function upItem(BannerItem $item): void
    {

        $items = [];
        foreach ($item->banner->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 1; $i < count($items); $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i - 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function downItem(BannerItem $item): void
    {
        $items = [];
        foreach ($item->banner->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 0; $i < count($items) - 1; $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i + 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i + 1]->update(['sort' => $next]);
            }
        }
    }

}
