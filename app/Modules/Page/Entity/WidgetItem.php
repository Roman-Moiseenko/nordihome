<?php

namespace App\Modules\Page\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sort
 * @property string $caption
 * @property string $description
 * @property string $slug
 * @property int $widget_id
 * @property Widget $widget
 */
abstract class WidgetItem extends Model
{
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $casts = [
        ];
        $fillable = [
            'widget_id',
            'sort',
            'caption',
            'description'
        ];
        $_attributes = [

        ];

        $this->casts = array_merge($this->casts, $casts);
        $this->fillable = array_merge($this->fillable, $fillable);
        $this->attributes = array_merge($this->attributes, $_attributes);
    }


    public static function new(int $widget_id): static
    {
        return self::make([
            'widget_id' => $widget_id,
            'sort' => self::where('widget_id', $widget_id)->count(),
        ]);
    }
}
