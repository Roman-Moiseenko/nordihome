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
        $arrival_ids = ArrivalDocument::where('supply_id', $this->supply_id)->pluck('id')->toArray();

        $quantity_arrival = ArrivalProduct::selectRaw('SUM(quantity) AS total')
            ->whereIn('arrival_id', $arrival_ids)
            ->where('product_id', $this->product_id)
            ->first();

       // dd([$quantity, $arrival_ids, $quantity_arrival]);
        return $quantity - ((int)$quantity_arrival->total ?? 0);
        /*foreach ($this->document->arrivals as $arrival) {
            $arrivalProduct = $arrival->getProduct($this->product_id);
            if (!is_null($arrivalProduct)) $quantity -= $arrivalProduct->getQuantity();
        }
        return $quantity; */
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
