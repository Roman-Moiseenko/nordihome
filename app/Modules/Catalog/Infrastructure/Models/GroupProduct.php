<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $group_id
 * @property int $product_id
 * @property float $price
 *
 * @property Group $group
 */
class GroupProduct extends Model
{
    public $timestamps = false;
    public $table = 'groups_products';

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
