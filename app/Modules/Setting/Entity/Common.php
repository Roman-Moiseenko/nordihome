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
    public bool $accounting = false;
    public int $group_last_id = -1;

    public function view()
    {
        $groups = Group::orderBy('name')->get()->map(function (Group $item) {
            return [$item->id] = $item->name;
        })->toArray();


        return view('admin.settings.common', ['common' => $this, 'groups' => $groups]);
    }
}
