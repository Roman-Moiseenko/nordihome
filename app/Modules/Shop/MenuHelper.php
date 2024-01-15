<?php
declare(strict_types=1);

namespace App\Modules\Shop;

class MenuHelper
{
    public static function getMenuPages(): array
    {
        //TODO Сделать загрузку из модели Page
        return [
            /* 'about' => [
                 'name' => 'О компании',
                 'icon' => '',
                 'page' => 'about',
                 'route_name' => 'shop.page.about',
             ],*/
            'tariff' => [
                'name' => 'Условия и тарифы',
                'icon' => '',
                'page' => 'tariff',
                'route_name' => 'shop.page.tariff',
            ],
            'reviews' => [
                'name' => 'Отзывы',
                'icon' => '',
                'page' => 'review',
                'route_name' => 'shop.page.review',
            ],
            'contact' => [
                'name' => 'Контакты',
                'icon' => '',
                'page' => 'contact',
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
                   'route_name' => 'shop.pre_order',

               ],*/
        ];
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
}
