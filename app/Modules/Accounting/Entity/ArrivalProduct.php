<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $arrival_id
 * @property int $product_id
 * @property int $quantity
 * @property int $remains //Остаток для фильтрации
 * @property float $cost_currency //В валюте документа
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
        'remains',
    ];

    //TODO $distributor_cost на $cost и cost_currency на cost (в рублях)!!!!
    public static function new(int $product_id, int $quantity, float $distributor_cost): self
    {
        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'cost_currency' => $distributor_cost,
            'remains' => $quantity,
        ]);
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->remains = $quantity;
    }

    public function setCost(float $cost_currency): void
    {
        $this->cost_currency = $cost_currency;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function getCostRu(): int
    {
        return (int)(ceil($this->cost_currency * $this->document->exchange_fix));
    }
}
