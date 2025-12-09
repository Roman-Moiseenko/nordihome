<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Menu;
use App\Modules\Page\Entity\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuService
{

    public function createMenu(Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        Menu::register($name, empty($slug) ? Str::slug($name) : $slug);
    }

    public function deleteMenu(Menu $menu): void
    {
        if ($menu->items()->count() > 0) throw new \DomainException('Нельзя удалить меню с элементами');
        $menu->delete();
    }

    public function addItem(Menu $menu, Request $request): void
    {
        $item = MenuItem::new(
            $request->string('name')->trim()->value(),
            $request->string('url')->trim()->value(),
        );
        $menu->items()->save($item);
        $menu->refresh();
    }

    public function moveItems(Menu $menu, Request $request): void
    {
        $new_sort = $request->input('new_sort');

        foreach ($new_sort as $i => $id) {
            $item = MenuItem::find($id);
            $item->sort = $i;
            $item->save();
        }
    }

    public function upItem(MenuItem $item)
    {

    }

    public function downItem(MenuItem $item)
    {

    }

    public function deleteItem(MenuItem $item): void
    {
        $menu = $item->menu;
        $item->delete();
        foreach ($menu->items as $index => $item) {
            $item->sort = $index;
            $item->save();
        }
    }

    public function setInfo(Menu $menu, Request $request): void
    {
        $name = $request->string('name')->trim()->value();
        $slug = $request->string('slug')->trim()->value();
        $menu->name = $name;
        $menu->slug = empty($slug) ? Str::slug($name) : $slug;
        $menu->save();
    }

    public function setItem(MenuItem $item, Request $request): void
    {
        $item->name = $request->string('name')->trim()->value();
        $item->url = $request->string('url')->trim()->value();
        $item->save();
        $item->refresh();
    }
}
