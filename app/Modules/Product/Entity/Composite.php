<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $parent_id
 * @property int $child_id
 * @property float $quantity
 * @property Product $parent
 * @property Product $child
 */

class Composite extends Model
{
    public $timestamps = false;
    protected $table = 'composites';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id', 'id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'child_id', 'id');
    }

}
