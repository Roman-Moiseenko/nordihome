<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property array $dimensions
 * @property SizeGridValue[] $values
 */
class SizeGrid extends Model
{
    public $timestamps = false;

    protected $table = 'size_grids';

    protected $fillable = [
        'name',
        'dimensions',
    ];

    protected $casts = [
        'dimensions' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(SizeGridValue::class, 'size_grid_id', 'id');
    }
}
