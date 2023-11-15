<?php
declare(strict_types=1);

namespace App\Menu;

class ContactMenu
{
    public static function menu()
    {
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
