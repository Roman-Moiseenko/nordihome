<?php


namespace App\Forms;


class Input extends BaseForm
{
    public string $type = 'text';
    public string $group = '';
    public string $help = '';
    public bool $pos_left = true;
    public string $group_text = '';



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

    public function help($help): self
    {
        $input = clone $this;
        $input->help = $help;
        return $input;
    }

    public function group_text($group_text, $pos_left = true)
    {
        $input = clone $this;
        $input->group_text = $group_text;
        $input->pos_left = $pos_left;
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
            'help' => $this->help,
            'group_text' => $this->group_text,
            'pos_left' => $this->pos_left,
        ]);
        return view('forms.input', $params);
    }
}
