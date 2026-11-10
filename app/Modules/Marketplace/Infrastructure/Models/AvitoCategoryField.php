<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $avito_category_id
 * @property string $tag
 * @property string $name
 * @property string $type
 * @property bool $is_required
 * @property array|null $allowed_values
 * @property array|null $depends_on
 *
 */
class AvitoCategoryField extends Model
{
    protected $fillable = [
        'avito_category_id',
        'tag',
        'name',
        'type',
        'is_required',
        'allowed_values',
        'depends_on',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'allowed_values' => 'array',
        'depends_on' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AvitoCategory::class, 'avito_category_id');
    }
}
