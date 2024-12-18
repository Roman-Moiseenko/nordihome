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
 * @property float $pre_cost
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
        'pre_cost',
    ];

    public static function new(int $product_id, float $quantity, float $distributor_cost): self
    {
        $product = self::baseNew($product_id, $quantity);
        $product->cost_currency = $distributor_cost;
        $product->pre_cost = $distributor_cost;
        return $product;
    }

    /**
     * Кол-во текущего товара не распределенное по документам на основании (Поступление, Возврат)
     */
    public function getQuantityUnallocated(): float
    {
        $quantity = $this->quantity;
        //Поступило
        $arrival_ids = ArrivalDocument::where('supply_id', $this->supply_id)->pluck('id')->toArray();

        $quantity_arrival = ArrivalProduct::selectRaw('SUM(quantity) AS total')
            ->whereIn('arrival_id', $arrival_ids)
            ->where('product_id', $this->product_id)
            ->first();

        return $quantity - ((float)$quantity_arrival->total ?? 0);
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

    public function toArray(): array
    {
        $array = parent::toArray();
        return array_merge($array, [
            'cost_currency' => (float)$this->cost_currency,
            'pre_cost' => (float)$this->pre_cost,
        ]);
    }
}
