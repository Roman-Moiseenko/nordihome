<?php
declare(strict_types=1);

namespace App\Modules\Discount\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $promotion_id
 * @property int $product_id
 * @property int $price
 * @property Promotion $promotion
 */
class PromotionProduct extends Model
{
    public $timestamps = false;
    protected $touches = ['promotion'];
    public $incrementing = false;

    protected $table = 'promotions_products';


    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }
}
