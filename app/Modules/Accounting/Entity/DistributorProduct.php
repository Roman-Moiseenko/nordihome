<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $distributor_id
 * @property int $product_id
 * @property float $cost
 * @property float $pre_cost
 * @property Product $product
 * @property Distributor $distributor
 */
class DistributorProduct extends Model
{

    public $timestamps = false;
    protected $table = 'distributors_products';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withTrashed();
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        return array_merge($array, [
            'cost' => (float)$this->cost,
            'pre_cost' => (float)$this->pre_cost,
        ]);
    }
}
