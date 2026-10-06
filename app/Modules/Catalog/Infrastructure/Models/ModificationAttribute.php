<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $modification_id
 * @property int $attribute_id
 * @property int $sort
 * @property Attribute $attribute
 */
class ModificationAttribute extends Model
{
    public $timestamps = false;

    protected $table = 'modification_attributes';

    protected $fillable = [
        'modification_id',
        'attribute_id',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }
}
