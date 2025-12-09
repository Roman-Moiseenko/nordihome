<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity\Widgets;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $url
 * @property BannerWidget $widget
 */
class BannerWidgetItem extends WidgetItem
{
    use ImageField;

    protected $table= "widget_banner_items";

    public static function register(int $widget_id): self
    {
        $item = parent::new($widget_id);
        $item->save();
        return $item;
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(BannerWidget::class, 'widget_id', 'id');
    }
}
