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
        return view('forms.select', [
            'id' => $this->id,
            'class' => $this->class,
            'name' => $this->name,
            'selected' => $this->value,
            'label' => $this->label,
            'label_pos' => $this->label_pos,
            'label_description' => $this->label_description,
            'placeholder' => $this->placeholder,
            'options' => $this->options,
            'message' => $this->message,
            'disabled' => $this->disabled,
        ]);
    }

}
