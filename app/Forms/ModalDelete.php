<?php
declare(strict_types=1);

namespace App\Forms;

class ModalDelete
{
    private array $params;

    /**
     * Входные данные - id, caption, text
     * Считываем с <a> - data-tw-toggle="modal", data-route="..."
     */

    public static function create($caption, $text, $id = 'delete-confirmation-modal'): self
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
}
