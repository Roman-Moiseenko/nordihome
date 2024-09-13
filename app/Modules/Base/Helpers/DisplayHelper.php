<?php
declare(strict_types=1);

namespace App\Modules\Base\Helpers;

class DisplayHelper
{
    public static function badge(string $text, string $bg, string $color = 'text-white'): string
    {
        return '<span class="p-1 rounded-lg ' . $bg . ' ' . $color .'">' . $text . '</span>';
    }
}
//composer remove rappasoft/laravel-livewire-tables
