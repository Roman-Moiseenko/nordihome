<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Menu\MenuItemData;
use App\Modules\Shop\Application\DTOs\Menu\MenuData;
use Illuminate\Support\Facades\DB;

class MenuQueryRepository
{
    /** @return MenuData[] */
    public function getMenusWithItems(): array
    {
        $rows = DB::table('menus')
            ->leftJoin('menu_items', 'menus.id', '=', 'menu_items.menu_id')
            ->select(
                'menus.id as menu_id',
                'menus.slug as menu_slug',
                'menus.name as menu_name',
                'menu_items.id as item_id',
                'menu_items.name as item_name',
                'menu_items.url as item_url',
                'menu_items.svg as item_svg',
                'menu_items.sort as item_sort'
            )
            ->orderBy('menus.id')
            ->orderBy('menu_items.sort')
            ->get();

        $itemsByMenu = [];
        foreach ($rows as $row) {
            if ($row->item_id) {
                $itemsByMenu[$row->menu_id][] = new MenuItemData(
                    id: (int)$row->item_id,
                    name: $row->item_name,
                    url: $row->item_url ?? '',
                    svg: $row->item_svg ?? '',
                    sort: (int)$row->item_sort,
                );
            }
        }

        $menus = [];
        foreach ($rows as $row) {
            if (!isset($menus[$row->menu_id])) {
                $items = $itemsByMenu[$row->menu_id] ?? [];
                $menus[$row->menu_id] = new MenuData(
                    id: (int)$row->menu_id,
                    slug: $row->menu_slug,
                    name: $row->menu_name,
                    items: $items
                );
            }
        }

        return array_values($menus);
    }
}
