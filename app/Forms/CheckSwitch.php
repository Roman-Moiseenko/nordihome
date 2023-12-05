<?php
declare(strict_types=1);

namespace App\Forms;

class CheckSwitch extends BaseForm
{
    public function show()
    {
        $this->id = $id ?? 'checkbox-' . $this->name;
        $params = array_merge($this->loadParams(), [
        ]);

        return view('forms.check-switch', $params);
    }
}
