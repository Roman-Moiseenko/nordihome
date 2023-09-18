<?php


namespace App\Forms;


abstract class BaseForm
{
    public string $id;
    public string $type = '';
    public string $name;
    public string $value;
    public string $class;
    public string $label = '';
    public string $label_pos = '';
    public string $label_description = '';
    public bool $validate = false;
    public string $placeholder = '';
    public string $message = 'Ошибка валидации';
    public string $disabled = '';


    public static function create($name, array $attr = []): self
    {
        $form = new static();
        $form->name = $name;
        if (!empty($attr)) {
            $form->value = $attr['value'] ?? '';
            $form->class = $attr['class'] ?? '';
            if (!empty($attr['type'])) $form->type = $attr['type'];
            $form->placeholder = $attr['placeholder'] ?? '';
        }
        return $form;
    }

    public function label($label, $description = '',  $pos = 'left'): self
    {
        $form = clone $this;
        $form->label = $label;
        $form->label_description = $description;
        $form->label_pos = $pos;
        return $form;
    }

    public function validate($message): self
    {
        $form = clone $this;
        $form->validate = true;
        $form->message = $message;
        return $form;
    }

    public function disabled($event = false): self
    {
        $form = clone $this;
        $form->disabled = $event ? 'disabled' : '';
        return $form;
    }

    public function loadParams(): array
    {
        return [
            'id' => $this->id,
            'class' => $this->class,
            'type' => $this->type,
            'name' => $this->name,
            'value' => $this->value,
            'label' => $this->label,
            'label_pos' => $this->label_pos,
            'label_description' => $this->label_description,
            'placeholder' => $this->placeholder,
            'message' => $this->message,
            'disabled' => $this->disabled,
        ];
    }

    //TODO Сделать массив базовых аттрибутов, в наследниках объединять массивы
    abstract public function show();
}
