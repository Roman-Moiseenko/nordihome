<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property string $template
 * @property string $caption
 * @property string $description
 * @property string $url
 * @property array $params -?
 * @property int $banner_id
 * @property BannerWidget $banner
 * @property ProductWidgetItem[] $items
 */
class ProductWidget extends Widget
{
    //TODO Миграция переименовать widget_products
    protected $table="widgets";



    public $timestamps = false;
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
