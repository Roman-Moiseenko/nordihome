<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $movement_id
 * @property int $product_id
 * @property int $quantity
 *
 * @property Product $product
 * @property MovementDocument $document
 */
class MovementProduct extends Model implements MovementItemInterface
{
    protected $table = 'movement_products';
    public $timestamps = false;
    protected $fillable = [
        'movement_id',
        'product_id',
        'quantity',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(MovementDocument::class, 'movement_id', 'id');

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
