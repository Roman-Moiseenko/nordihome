<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $arrival_id
 * @property int $remains //Остаток для фильтрации
 * @property float $cost_currency //В валюте документа
 * @property int $supply_product_id //На основе Заказа
 * @property ArrivalDocument $document
 */
class ArrivalProduct extends AccountingProduct implements MovementItemInterface
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
    public function getQuantityUnallocated(): int
    {
        $quantity = $this->quantity;

        //Возврат
        foreach ($this->document->refunds as $refund) {
            $refundProduct = $refund->getProduct($this->product_id);
            if (!is_null($refundProduct)) $quantity -= $refundProduct->getQuantity();
        }
        return $quantity;
    }
}
