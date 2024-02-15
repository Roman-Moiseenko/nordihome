<?php
declare(strict_types=1);

namespace App\Forms;

class ModalPassword
{
    private array $params;

    /**
     * Входные данные - id, caption, text
     * Считываем с <a> - data-tw-toggle="modal", data-route="..."
     */

    public static function create($id = 'modal-change-password'): self
    {
        $modal = new static();
        $modal->params = [
            'id' => $id,
        ];
        return $modal;
    }

    public function show()
    {
        return view('forms.modal-password', $this->params);
    }
}
