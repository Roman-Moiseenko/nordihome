<?php
declare(strict_types=1);

namespace App\Forms;

class TomSelect extends BaseForm
{
    public array $options = [];
    public array $selected = [];
    public string $header;
    //public bool $notkeys;

    public function header(string $header): self
    {
        $select = clone $this;
        $select->header = $header;
        return $select;

    }

    public function options(array $options): self
    {
        $select = clone $this;
        $select->options = $options;
//        $select->notkeys = $notkeys;
        return $select;
    }

    public function selected(array $selected): self
    {
        $select = clone $this;
        $select->selected = $selected;
        $select->value = '';
        return $select;
    }


    public function show($id = null)
    {
        $this->id = $id ?? 'select-' . $this->name;
        $params = array_merge($this->loadParams(), [
            'selected' => $this->selected,
            'options' => $this->options,
            'header' => $this->header,
  //          'notkeys' => $this->notkeys,
        ]);
        return view('forms.tom-select', $params);
    }
}
