<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity\Widgets;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $url
 * @property array $params -?
 * @property int $banner_id
 * @property BannerWidget $banner
 * @property ProductWidgetItem[] $items
 */
class ProductWidget extends Widget
{
    protected $table = "widget_products";

    protected $attributes = [
        'params' => '{}',
    ];

    public $fillable = [
        'params',
        'url'
    ];

    protected $casts = [
        'params' => 'json'
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(BannerWidget::class, 'banner_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductWidgetItem::class, 'widget_id', 'id')->orderBy('sort');
    }


}
