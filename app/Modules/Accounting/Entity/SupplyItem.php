<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $supply_id
 * @property int $product_id
 * @property int $quantity
 * @property Supply $supply
 */
class SupplyItem extends Model
{
    protected $table = 'supply_items';

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
}
