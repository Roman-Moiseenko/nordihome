<?php

namespace App\Modules\Content\Infrastructure\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $container_type
 * @property int $container_id
 * @property int $widget_instance_id
 * @property int $sort
 * @property string $section
 * @property string $caption
 * @property ?DateTime $created_at
 * @property ?DateTime $updated_at
 * @property WidgetInstance $widgetInstance
 */
class ContentBlock extends Model
{
    protected $fillable = [
        'container_type',
        'container_id',
        'widget_instance_id',
        'parent_block_id',
        'sort_order',
        'section',
        'caption',
    ];
    protected $casts = [
        'sort_order' => 'integer',
    ];
    public function widgetInstance(): BelongsTo
    {
        return $this->belongsTo(WidgetInstance::class);
    }
    public function container(): MorphTo
    {
        return $this->morphTo('container', 'container_type', 'container_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_block_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_block_id')->orderBy('sort_order');
    }

}
