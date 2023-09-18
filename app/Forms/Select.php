<?php


namespace App\Forms;


class Select extends BaseForm
{
    public array $options;

    public function options(array $options): self
    {
        $select = clone $this;
        $select->options = $options;
        return $select;
    }

    public function show($id = null)
    {
        $this->id = $id ?? 'select-' . $this->name;
        $params = array_merge($this->loadParams(), [
            'selected' => $this->value,
            'options' => $this->options,
        ]);
        return view('forms.select', $params);
    }

}
