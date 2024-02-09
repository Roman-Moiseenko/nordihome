<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Page\Entity\Page;

class MenuHelper
{
    public static function getMenuPages(): array
    {
        $pages = Page::where('published', true)->where('parent_id', null)->where('menu', true)->orderBy('sort')->getModels();
        $add_items = [
            [
                'name' => 'Заказ товаров из ИКЕА',
                'icon' => '',
                'route' => route('shop.parser.view'),
            ],
        ];

        return array_merge(array_map(function (Page $page) {
            return [
                'name' => $page->name,
                'icon' => '', //TODO Возможно сделать иконку для Page
                'route' => route('shop.page.view', $page->slug),
            ];

        }, $pages), $add_items);

    }

    public static function getMenuContacts(): array
    {
        //TODO Сделать настройку контактов в админке и загрузке из  базы
        return [
            'phone' => [
                'name' => 'Позвонить по телефону',
                'icon' => 'fa-sharp fa-solid fa-circle-phone',
                'color' => '#000000',
                'url' => 'tel:+74012373730',
                'data-type' => 1,
            ],
            'telegram' => [
                'name' => 'Написать в телеграм',
                'icon' => 'fa-brands fa-telegram',
                'color' => '#000000',
                'url' => 'https://t.me/Manager1_euroikea',
                'data-type' => 2,
            ],
            'vk' => [
                'name' => 'Сообщество в ВК',
                'icon' => 'fa-brands fa-vk',
                'color' => '#000000',
                'url' => 'https://vk.com/nordihome',
                'data-type' => 3,
            ],
            'whatsapp' => [
                'name' => 'Написать в Ватцап',
                'icon' => 'fa-brands fa-whatsapp',
                'color' => '#000000',
                'url' => 'https://wa.me/+79062108505?text=Здравствуйте, я хочу мебель из Икеа!',
                'data-type' => 4,
            ],
        ];
    }

    public static function getFooterMenu(): array
    {
        return [
            'column-1' => [
                'title' => 'Меню',
                'items' => array_merge(
                [
                    [
                        'name' => 'Каталог товаров',
                        'icon' => '',
                        'route' => route('shop.category.index'),
                    ],
                ],
                    self::getMenuPages()
                ),
            ],
            'column-2' => [
                'title' => 'Для клиента',
                'items' => [//TODO сделать аналог self::getMenuPages(),
                    [
                        'name' => 'Условия использования сайта',
                        'icon' => '',
                        'route' => route('shop.page.view', 'condition'),
                    ],
                    [
                        'name' => 'Политика обработки персональных данных',
                        'icon' => '',
                        'route' => route('shop.page.view', 'political'),
                    ],
                ],
            ],

        ];
    }

    public static function getCabinetMenu(): array
    {
        return [
            'cabinet' => [
                'name' => 'Личный кабинет',
                'icon' => 'fa-light fa-user-vneck',
                'url' => route('cabinet.view'),
            ],
            'orders' => [
                'name' => 'Мои заказы',
                'icon' => 'fa-sharp fa-light fa-box-open',
                'url' => route('cabinet.order.index'),
            ],
            'wish' => [
                'name' => 'Избранное',
                'icon' => 'fa-light fa-heart',
                'url' => route('cabinet.wish.index'),
            ],
            'cart' => [
                'name' => 'Корзина',
                'icon' => 'fa-light fa-cart-shopping',
                'url' => route('shop.cart.view'),
            ],
        ];
    }
}
