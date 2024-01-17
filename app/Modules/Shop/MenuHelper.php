<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Page\Entity\Page;

class MenuHelper
{
    public static function getMenuPages(): array
    {
        //TODO Сделать загрузку из модели Page

        $pages = Page::where('published', true)->where('parent_id', null)->where('menu', true)->orderBy('sort')->getModels();


        return array_map(function (Page $page) {
            return [
                'name' => $page->name,
                'icon' => '', //TODO Возможно сделать иконку для Page
                'route' => route('shop.page.view', $page->slug),
            ];

        }, $pages);

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
                'items' => self::getMenuPages(),
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
}
