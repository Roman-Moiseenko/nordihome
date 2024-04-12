<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $supply_id
 * @property int $product_id
 * @property int $quantity
 * @property SupplyDocument $supply
 * @property Product $product
 */
class SupplyProduct extends Model
{
    protected $table = 'supply_products';
    public $timestamps = false;
    protected $fillable = [
        'supply_id',
        'product_id',
        'quantity'
    ];

    public static function new(int $product_id, int $quantity): self
    {
        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
    }

    public function supply()
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
