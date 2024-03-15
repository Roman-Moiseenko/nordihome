<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $departure_id
 * @property int $product_id
 * @property int $quantity
 * @property float $cost
 * @property Product $product
 * @property DepartureDocument $document
 */
class DepartureProduct extends Model implements MovementItemInterface
{
    protected $table = 'departure_products';
    public $timestamps = false;
    protected $fillable = [
        'departure_id',
        'product_id',
        'quantity',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(DepartureDocument::class, 'departure_id', 'id');
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
