<?php

namespace App\Modules\Content\Infrastructure\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $widget_id
 * @property array $params
 * @property string $title
 * @property ?DateTime $created_at
 * @property ?DateTime $updated_at
 * @property Widget $widget
 */
class WidgetInstance extends Model
{
    protected $fillable = ['widget_id', 'params', 'title'];
    protected $casts = [
        'params' => 'array',
    ];

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }

    public function contentBlocks()
    {
        return $this->hasMany(ContentBlock::class);
    }
}
