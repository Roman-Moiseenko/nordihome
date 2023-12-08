<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $width
 * @property int $height
 * @property int $depth
 * @property int $weight
 */
class ProductDelivery extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'width',
        'height',
        'depth',
        'weight'
    ];

    public static function register(int $product_id, int $width, int $height, int $depth, int $weight): self
    {
        return self::create([
            'product_id' => $product_id,
            'width' => $width,
            'height' => $height,
            'depth' => $depth,
            'weight' => $weight
        ]);
    }
}
