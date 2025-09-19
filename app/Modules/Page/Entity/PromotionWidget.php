<?php

namespace App\Modules\Page\Entity;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Discount\Entity\Promotion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $banner_id
 * @property int $promotion_id
 * @property Promotion $promotion
 * @property BannerWidget $banner
 */
class PromotionWidget extends Widget
{

    protected $table="widget_promotions";

    use ImageField, IconField;


    public function banner(): BelongsTo
    {
        return $this->belongsTo(BannerWidget::class, 'banner_id', 'id');
    }
}
