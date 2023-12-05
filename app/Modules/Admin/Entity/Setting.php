<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property SettingItem[] $items
 */
class Setting extends Model
{
    public $timestamps = false;

    public static function register(string $name, string $slug): self
    {
        $setting = new Setting();
        $setting->name = $name;
        $setting->slug = $slug;
        $setting->save();
        return $setting;
    }

    public function items()
    {
        return $this->hasMany(SettingItem::class, 'setting_id', 'id');
    }
}
