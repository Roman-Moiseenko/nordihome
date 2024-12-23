<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $arrival_id
 * @property int $remains //Остаток для фильтрации
 * @property float $cost_currency //В валюте документа
 * @property int $supply_product_id //На основе Заказа
 * @property ArrivalDocument $document
 */
class ArrivalProduct extends AccountingProduct
{
    protected $table = 'arrival_products';
    protected $fillable = [
        'arrival_id',
        'product_id',
        'quantity',
        'cost_currency',
        'remains',
        'supply_product_id',
    ];

    public static function new(int $product_id, float $quantity, float $distributor_cost): self
    {
        $product = self::baseNew($product_id, $quantity);
        $product->cost_currency = $distributor_cost;
        $product->remains = $product->quantity;
        return $product;
    }

    public function setQuantity(float $quantity): void
    {
        parent::setQuantity($quantity);
        $this->refresh();

        $this->remains = $this->quantity;
        $this->save();
    }

    public function setCost(float $cost_currency): void
    {
        $this->cost_currency = $cost_currency;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function getCostRu(): int
    {
        return (int)(ceil($this->cost_currency * $this->document->exchange_fix));
    }

    /**
     * @return SupplyProduct
     */
    public function getSupplyProduct(): AccountingProduct
    {
        return $this->document->supply->getProduct($this->product_id);
    }

    /**
     * Кол-во текущего товара оставшегося после возвратов
     */
    public function getQuantityUnallocated(): float
    {
        $quantity = (float)$this->quantity;

        //Возврат
        foreach ($this->document->refunds as $refund) {
            $refundProduct = $refund->getProduct($this->product_id);
            if (!is_null($refundProduct)) $quantity -= $refundProduct->getQuantity();
        }
        return $quantity;
    }

    /**
     * Кол-во текущего товара в перемещениях
     */
    public function getQuantityMoved(): float
    {
        $quantity = 0;
        foreach ($this->document->movements as $movement) {
            $movementProduct = $movement->getProduct($this->product_id);
            if (!is_null($movementProduct)) $quantity += $movementProduct->getQuantity();
        }
        return $quantity;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        return array_merge($array, [
            'cost_currency' => (float)$this->cost_currency,
        ]);
    }
}
