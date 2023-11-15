<?php
declare(strict_types=1);

namespace App\Menu;

use App\Modules\Product\Repository\CategoryRepository;

class ShopMenu
{
    public static function menu(): array
    {

        return [
            'about' => [
                'name' => 'О компании',
                'icon' => '',
                'route_name' => 'shop.page.about',
            ],
            'tariff' => [
                'name' => 'Условия и тарифы',
                'icon' => '',
                'route_name' => 'shop.page.tariff',
            ],
            'reviews' => [
                'name' => 'Отзывы',
                'icon' => '',
                'route_name' => 'shop.review',
            ],
            'contact' => [
                'name' => 'Контакты',
                'icon' => '',
                'route_name' => 'shop.page.contact',
            ],
         /*   'in_stock' => [
                'name' => 'Товары в наличии',
                'icon' => '',
                'route_name' => 'shop.categories',
                'submenu' => (new CategoryRepository())->getTree(),
            ],
            'pre_order' => [
                'name' => 'Товары подзаказ',
                'icon' => '',
                'route_name' => 'shop.preorder',

            ],*/
        ];
    }
}
