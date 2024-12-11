<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;

/**
 * @property int $id
 * @property int $product_id
 * @property float $quantity - не используется в PricingDocument
 * @property int $service_id - для услуг
 *
 * @property Product $product
 */
abstract class AccountingProduct extends Model
{
    public $timestamps = false;

    /**
     * Объединяем базовые параметры
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $casts = [

        ];
        $fillable = [
            'product_id',
            'quantity',
        ];
        $attributes = [

        ];

        $this->casts = array_merge($this->casts, $casts);
        $this->fillable = array_merge($this->fillable, $fillable);
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public static function baseNew(int $product_id, float $quantity): static
    {
        $product = Product::find($product_id);
        if (!$product->isFractional()) $quantity = ceil($quantity);

        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Проверка на дробное, если только целое, то округляем в большую сторону
     */
    public function setQuantity(float $quantity): void
    {
        if (!$this->product->isFractional()) $quantity = ceil($quantity);
        $this->quantity = $quantity;
        $this->save();
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function addQuantity(float $delta): void
    {
        $this->setQuantity($delta + $this->quantity);
    }

    final public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    //Для создания таблиц
    final public static function columns(Blueprint $table): void
    {
        $table->decimal('quantity', 10, 3)->default(0.0);
        $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
    }

    final public static function dropColumns(Blueprint $table): void
    {
        $table->dropIndex('quantity');

        $table->dropForeign(['product_id']);
        $table->dropColumn('product_id');
    }
}
