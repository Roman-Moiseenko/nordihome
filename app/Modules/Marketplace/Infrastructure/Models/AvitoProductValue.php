<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $avito_product_id
 * @property int $avito_category_field_id
 * @property string|null $value
 */
class AvitoProductValue extends Model
{
    protected $table = 'avito_product_values';

    protected $fillable = [
        'avito_product_id',
        'avito_category_field_id',
        'value',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(AvitoProduct::class, 'avito_product_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(AvitoCategoryField::class, 'avito_category_field_id');
    }
}
