<?php


namespace App\Forms;


class Input extends BaseForm
{
    public string $type = 'text';
    public string $group = '';

    public function group($text): self
    {
        $input = clone $this;
        if (is_string($text)) $input->group = $text;
        if (is_array($text)) {
            $input->group = '<i data-lucide="' . $text['icon'] .
                '" width="' . $text['size'] . '" height="' . $text['size'] .
                '" class="' . ($text['class'] ?? '') .'" ></i>';
        }
        return $input;
    }

    public function type($type): self
    {
        $input = clone $this;
        $input->type = $type;
        return $input;
    }

    public function show($id = null)
    {
        $this->id = $id ?? 'input-' . $this->name;
        $params = array_merge($this->loadParams(), [
            'group' => $this->group,
        ]);
        return view('forms.input', $params);
    }
}
