<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $departure_id
 * @property float $cost - в рублях
 * @property DepartureDocument $document
 */
class DepartureProduct extends AccountingProduct
{
    protected $table = 'departure_products';
    public $timestamps = false;
    protected $fillable = [
        'departure_id',
        'cost',
    ];

    public static function new(int $product_id, float $quantity, float $cost): self
    {
        $product = self::baseNew($product_id, $quantity);
        $product->cost = $cost;
        return $product;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DepartureDocument::class, 'departure_id', 'id');
    }

}
