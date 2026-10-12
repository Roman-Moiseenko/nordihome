<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $size_grid_id
 * @property string $dimension_code
 * @property string $value
 * @property string|null $label
 * @property int $sort_order
 * @property bool $is_active
 */
class SizeGridValue extends Model
{
    public $timestamps = false;

    protected $table = 'size_grid_values';

    protected $fillable = [
        'size_grid_id',
        'dimension_code',
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function sizeGrid(): BelongsTo
    {
        return $this->belongsTo(SizeGrid::class, 'size_grid_id', 'id');
    }
}
