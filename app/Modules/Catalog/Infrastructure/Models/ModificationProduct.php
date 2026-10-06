<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $modification_id
 * @property int $product_id
 * @property bool $is_primary
 * @property Modification $modification
 * @property Product $product
 * @property ModificationProductValue[] $values
 */
class ModificationProduct extends Model
{
    protected $table = 'modifications_products';

    protected $fillable = [
        'modification_id',
        'product_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function register(int $modification_id, int $product_id, array $variants, bool $is_primary = false): self
    {
        $element = self::create([
            'modification_id' => $modification_id,
            'product_id' => $product_id,
            'is_primary' => $is_primary,
        ]);

        foreach ($variants as $attribute_id => $variant_id) {
            $element->values()->create([
                'attribute_id' => (int) $attribute_id,
                'variant_id' => (int) $variant_id,
            ]);
        }

        return $element;
    }

    public function modification(): BelongsTo
    {
        return $this->belongsTo(Modification::class, 'modification_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ModificationProductValue::class, 'modification_product_id', 'id');
    }
}
