<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

use App\Modules\Product\Entity\Group;

class Common extends AbstractSetting
{
    public int $reserve = 0;
    public bool $pre_order = false;
    public bool $only_offline = false;
    public bool $delivery_local = false;
    public bool $delivery_all = false;
    //TODO Удалить, товарный учет всегда для СРМ
    public bool $accounting = true;
    public int $group_last_id = -1;
    public string $date_bank = '01.01.1900';

    public function view()
    {
        $groups[0] = '';
        $_groups = Group::orderBy('name')->get();
        foreach ($_groups as $item) {
            $groups[$item->id] = $item->name;
        };
        return view('admin.settings.common', ['common' => $this, 'groups' => $groups]);
    }
}
