<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $storage_id
 * @property int $product_id
 * @property int $quantity
 *
 * @property Product $product
 */
class StorageItem extends Model
{
    protected $table = 'storage_items';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
