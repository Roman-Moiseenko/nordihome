<?php
declare(strict_types=1);

namespace App\Modules\Product\Helper;

class ProductHelper
{
    public static function menuAddProduct(): array
    {
        return [
            'common' => [
                'include' => '_common',
                'caption' => 'Общие параметры',
                'anchor' => 'common'
            ],
            'description' => [
                'include' => '_description',
                'caption' => 'Описание',
                'anchor' => 'description'
            ],
        ];
    }


    public static function menuUpdateProduct(): array
    {
        return [
            'common' => [
                'livewire' => false,
                'include' => '_common',
                'caption' => 'Общие параметры',
                'anchor' => 'common',
            ],
            'description' => [
                'livewire' => false,
                'include' => '_description',
                'caption' => 'Описание',
                'anchor' => 'description'
            ],
            'dimensions' => [
                'livewire' => true,
                'include' => '_dimensions',
                'caption' => 'Габариты и доставка',
                'anchor' => 'dimensions'
            ],
            'images' => [
                'livewire' => false,
                'include' => '_images',
                'caption' => 'Изображения',
                'anchor' => 'images'
            ],
            'video' => [
                'livewire' => true,
                'include' => '_video',
                'caption' => 'Видео обзоры',
                'anchor' => 'video'
            ],
            'attributes' => [
                'livewire' => false,
                'include' => '_attributes',
                'caption' => 'Атрибуты',
                'anchor' => 'attributes'
            ],
            'management' => [
                'livewire' => true,
                'include' => '_management',
                'caption' => 'Управление',
                'anchor' => 'management'
            ],
            'modification' => [
                'livewire' => false,
                'include' => '_modification',
                'caption' => 'Модификации',
                'anchor' => 'modification'
            ],
            'equivalent' => [
                'livewire' => false,
                'include' => '_equivalent',
                'caption' => 'Аналоги',
                'anchor' => 'equivalent'
            ],

            'related' => [
                'livewire' => true,
                'include' => '_related',
                'caption' => 'Сопутствующие',
                'anchor' => 'related'
            ],
            'bonus' => [
                'livewire' => true,
                'include' => '_bonus',
                'caption' => 'Бонусные товары',
                'anchor' => 'bonus'
            ],
            'option' => [
                'livewire' => false,
                'include' => '_option',
                'caption' => 'Опции',
                'anchor' => 'option'
            ],
            'composite' => [
                'livewire' => false,
                'include' => '_composite',
                'caption' => 'Составной товар',
                'anchor' => 'composite'
            ],

        ];
    }
}
