<?php
declare(strict_types=1);

namespace App\Forms;

class DatePicker extends Input
{

    public function show($id = null)
    {
        $this->id = $id ?? 'input-' . $this->name;
        $params = array_merge($this->loadParams(), [
            'group' => $this->group,
            'help' => $this->help,
            'group_text' => $this->group_text,
            'pos_left' => $this->pos_left,
        ]);
        return view('forms.date-picker', $params);
    }
}
