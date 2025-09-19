<?php

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sort
 * @property int $widget_id
 * @property Widget $widget
 */
abstract class WidgetItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'widget_id',
        'sort',
    ];

    public static function register(int $widget_id): self
    {
        return self::create([
            'widget_id' => $widget_id,
            'sort' => self::where('widget_id', $widget_id)->count(),
        ]);
    }
}
