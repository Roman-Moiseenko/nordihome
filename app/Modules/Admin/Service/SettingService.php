<?php
declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Modules\Admin\Entity\Setting;
use App\Modules\Admin\Entity\SettingItem;
use Illuminate\Http\Request;

class SettingService
{

    public static function group(array $options, array $items)
    {
        foreach ($items as $item) {
            $data = array_merge($options, $item);
            SettingItem::create($data);
        }
    }


    public function set(Request $request, Setting $setting)
    {
        $setting_id = $setting->id; //(int)$request['setting_id'];
        $keys = $request->all();

        $items = SettingItem::where('setting_id', $setting_id)->get();
        foreach ($items as $_item) {
            if ($_item->type == SettingItem::KEY_BOOL) {
                $_item->value = false;
                $_item->save();
            }
        }

        foreach ($keys as $key => $value) {
            $item = SettingItem::where('setting_id', $setting_id)->where('key', $key)->first();
            if ($item) {
                $this->updateItem($item, $value);
            }
        }
    }

    private function updateItem(SettingItem $item, string $value)
    {
        if ($item->type == SettingItem::KEY_INTEGER) {
            $item->value = (int)$value;
        }
        if ($item->type == SettingItem::KEY_BOOL) {
            $item->value = true;
        }
        if ($item->type == SettingItem::KEY_FLOAT) {
            $item->value = (float)$value;
        }
        if ($item->type == SettingItem::KEY_STRING) {
            $item->value = $value;
        }
        $item->save();
    }
}
