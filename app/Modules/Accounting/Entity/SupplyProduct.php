<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $supply_id
 * @property float $cost_currency
 * @property SupplyDocument $document
 * @property ArrivalProduct[] $arrivalProducts
 * @property
 */
class SupplyProduct extends AccountingProduct
{
    protected $table = 'supply_products';
    protected $fillable = [
        'supply_id',
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

        //Поступило
        foreach ($this->document->arrivals as $arrival) {
            $arrivalProduct = $arrival->getProduct($this->product_id);
            if (!is_null($arrivalProduct)) $quantity -= $arrivalProduct->getQuantity();
        }
        //Возврат
        foreach ($this->document->refunds as $refund) {
            $refundProduct = $refund->getProduct($this->product_id);
            if (!is_null($refundProduct)) $quantity -= $refundProduct->getQuantity();
        }
        return $quantity;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
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
