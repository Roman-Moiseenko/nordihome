<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Common extends AbstractSetting
{
    public int $reserve = 0;
    public bool $pre_order = false;
    public bool $only_offline = false;
    public bool $delivery_local = false;
    public bool $delivery_all = false;
    public bool $accounting = false;

    public function view()
    {
        return view('admin.settings.common', ['common' => $this]);
    }
}
