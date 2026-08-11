<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $url
 * @property string $marking
 * @property string $button
 * @property BannerWidget $widget
 */
class BannerWidgetItem extends WidgetItem
{
    use ImageField;

    protected $table= "widget_banner_items";

    protected $fillable = [
        'url',
        'marking',
        'button'
    ];
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
