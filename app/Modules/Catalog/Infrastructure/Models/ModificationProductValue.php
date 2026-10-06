<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $modification_product_id
 * @property int $attribute_id
 * @property int $variant_id
 * @property Attribute $attribute
 * @property AttributeVariant $variant
 */
class ModificationProductValue extends Model
{
    public $timestamps = false;

    protected $table = 'modification_product_values';

    protected $fillable = [
        'modification_product_id',
        'attribute_id',
        'variant_id',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(AttributeVariant::class, 'variant_id', 'id');
    }
}
