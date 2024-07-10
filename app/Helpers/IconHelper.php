<?php
declare(strict_types=1);

namespace App\Helpers;

use JetBrains\PhpStorm\ArrayShape;

class IconHelper
{
    public static function trash(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="trash-2" class="lucide lucide-trash-2 stroke-1.5 w-4 h-4"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>';
    }

    #[ArrayShape(['class' => "string", 'data-tw-toggle' => "string", 'data-tw-target' => "string", 'data-route' => "string"])]
    public static function attributeTrash(string $route): array
    {
        return [
            'class' => 'flex items-center text-danger',
            //Вызов Модального окна со ссылкой на удаление.
            'data-tw-toggle' => "modal",
            'data-tw-target' => "#delete-confirmation-modal",
            'data-route' => $route,
        ];
    }
}
