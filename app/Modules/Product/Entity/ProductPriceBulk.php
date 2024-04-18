<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property float $value
 * @property string $founded
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductPriceBulk extends Model
{
    protected $table = 'product_prices_bulk';
    protected $fillable = [
        'value',
        'founded'
    ];

    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
