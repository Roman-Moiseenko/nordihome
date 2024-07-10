<?php
declare(strict_types=1);

namespace App\Forms;

use JetBrains\PhpStorm\ArrayShape;

class ModalDelete
{
    private array $params;

    /**
     * Входные данные - id, caption, text
     * Считываем с <a> - data-tw-toggle="modal", data-route="..."
     */

    public static function create($caption, $text, $id = 'delete-confirmation-modal', array $buttons = []): self
    {
        $modal = new static();
        $modal->params = [
            'caption' => $caption,
            'text' => $text,
            'id' => $id,
        ];
        return $modal;
    }

    public function show()
    {
        return view('forms.modal-delete', $this->params);
    }

    /**
     * Атрибуты для ссылки вызова модального окна в livewire-Таблице
     */
    #[ArrayShape(['class' => "string", 'data-tw-toggle' => "string", 'data-tw-target' => "string", 'data-route' => "string"])]
    public static function attributes(string $route, string $target = 'delete-confirmation-modal'): array
    {
        return [
            'class' => 'flex items-center text-danger',
            //Вызов Модального окна со ссылкой на удаление.
            'data-tw-toggle' => "modal",
            'data-tw-target' => "#" . $target,
            'data-route' => $route,
        ];
    }
}
