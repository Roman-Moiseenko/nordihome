<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $supply_id
 * @property int $product_id
 * @property float $cost_currency
 * @property int $quantity
 * @property SupplyDocument $document
 * @property Product $product
 * @property ArrivalProduct[] $arrivalProducts
 */
class SupplyProduct extends Model
{
    protected $table = 'supply_products';
    public $timestamps = false;
    protected $fillable = [
        'supply_id',
        'product_id',
        'quantity',
        'cost_currency',
    ];

    public static function new(int $product_id, int $quantity, float $distributor_cost): self
    {
        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'cost_currency' => $distributor_cost
        ]);
    }

    /**
     * Кол-во текущего товара не распределенное по документам на основании (Поступление, Возврат)
     */
    public function getQuantityUnallocated(): int
    {
        $quantity = $this->quantity;
        foreach ($this->arrivalProducts as $arrivalProduct) {
            $quantity -= $arrivalProduct->quantity;
        }
        //TODO Возврат На будущее
        /*foreach ($this->refundProducts as $refundProduct) {
            $quantity -= $refundProduct->quantity;
        } */
        return $quantity;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getCostRu(): int
    {
        return (int)(ceil($this->cost_currency * $this->document->exchange_fix));
    }

    public function arrivalProducts(): HasMany
    {
        return $this->hasMany(ArrivalProduct::class, 'supply_product_id', 'id');
    }
}
