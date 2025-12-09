<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity\Widgets;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Product\Entity\Group;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $group_id
 * @property string $url
 * @property ProductWidget $widget
 * @property Group $group
 */
class ProductWidgetItem extends WidgetItem
{
    use ImageField;

    protected $table='widget_product_items';
    protected $fillable = [
        'group_id',
    ];

    public static function register(int $widget_id, int $group_id): self
    {
        $item = parent::new($widget_id);
        $item->group_id = $group_id;
        $item->save();
        return $item;
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(ProductWidget::class, 'widget_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
