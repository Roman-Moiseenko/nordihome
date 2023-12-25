<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Request;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $request_id
 * @property int $product_id
 * @property int $quantity
 * @property float $fix_price
 * @property float $new_price
 * @property bool $cancel
 */
class RequestItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'request_id',
        'product_id',
        'quantity',
        'fix_price',
        'new_price',
        'cancel'
    ];
}
