<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property SettingItem[] $items
 */

#[Deprecated]
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

    public static function get(string $slug): self
    {
        return Setting::where('slug', $slug)->first();
    }

    public function items()
    {
        return $this->hasMany(SettingItem::class, 'setting_id', 'id');
    }
}
