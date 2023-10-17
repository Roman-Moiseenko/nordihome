<?php
declare(strict_types=1);

namespace App\Forms;

class TextArea extends BaseForm
{

    public int $rows = 5;

    public function rows(int $rows): BaseForm
    {
        $textarea = clone $this;
        $textarea->rows = $rows;
        return $textarea;
    }
    public function show($id = null)
    {
        $this->id = $id ?? 'textarea-' . $this->name;
        $params = array_merge($this->loadParams(), [
            'rows' => $this->rows,
        ]);
        return view('forms.textarea', $params);
    }
}
