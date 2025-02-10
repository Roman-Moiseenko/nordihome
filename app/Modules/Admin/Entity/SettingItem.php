<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property int $setting_id
 * @property string $name
 * @property string $description
 * @property string $tab
 * @property string $key //$field
 * @property string $value //json
 * @property int $sort
 * @property int $type

 */

#[Deprecated]
class SettingItem extends Model
{
    const KEY_INTEGER = 1;
    const KEY_FLOAT = 2;
    const KEY_BOOL = 3;
    const KEY_STRING = 4;
    const KEY_VARIANTS = 5;
    const KEY_OBJECT = 6;
    const KEY_FILE = 7;

    const TABS = [
        'common' => 'Общие',
    ];

    public $timestamps = false;
    protected $fillable = [
        'setting_id',
        'name',
        'description',
        'tab',
        'key',
        'value',
        'type',
        'sort',
    ];
    protected $casts = [
        'value' => 'json',
    ];

}
