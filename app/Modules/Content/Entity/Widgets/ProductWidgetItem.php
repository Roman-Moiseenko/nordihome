<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $group_id
 * @property int $category_id
 * @property string $url
 * @property ProductWidget $widget
 * @property Group $group
 * @property Category $category
 */
class ProductWidgetItem extends WidgetItem
{
    use ImageField;

    protected $table='widget_product_items';
    protected $fillable = [
        'group_id',
        'category_id',
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
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');

    }
}
