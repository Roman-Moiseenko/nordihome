<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $arrival_id
 * @property int $product_id
 * @property int $quantity
 * @property float $cost_currency //В валюте документа
 * @property float $cost_ru //В рублях
 * @property int $price_sell //В рублях
 * @property Product $product
 * @property ArrivalDocument $document
 */
class ArrivalProduct extends Model implements MovementItemInterface
{
    protected $table = 'arrival_products';
    public $timestamps = false;
    protected $fillable = [
        'arrival_id',
        'product_id',
        'quantity',
        'cost_currency',
        'cost_ru',
        'price_sell'
    ];

    public static function new(int $product_id, int $quantity, float $distributor_cost, float $cost_ru, float $product_sell): self
    {
        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'cost_currency' => $distributor_cost,
            'cost_ru' => $cost_ru,
            'price_sell' => $product_sell,
        ]);
    }


    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');

    }
}
