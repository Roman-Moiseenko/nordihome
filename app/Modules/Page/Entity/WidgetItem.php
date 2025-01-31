<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Product\Entity\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $widget_id
 * @property int $group_id
 * @property string $slug
 * @property int $sort
 * @property string $caption
 * @property string $description
 * @property string $url
 * @property Widget $widget
 * @property Group $group
 */
class WidgetItem extends Model
{
    use ImageField;

    public $timestamps = false;
    protected $fillable = [
        'widget_id',
        'group_id',
        'sort',
    ];

    public static function register(int $widget_id, int $group_id): self
    {
        return self::create([
            'widget_id' => $widget_id,
            'group_id' => $group_id,
            'sort' => self::where('widget_id', $widget_id)->count(),
        ]);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class, 'widget_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
