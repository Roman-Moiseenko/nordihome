<?php

namespace App\Modules\Accounting\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property int $retail
 * @property float $sell_ikea
 * @property int $bulk
 * @property bool $progress
 */
class PriceOutbox extends Model
{
    public $timestamps = false;

    protected $table = 'price_outbox';

    protected $fillable = [
        'code',
        'retail',
        'sell_ikea',
        'bulk',
        'progress',
    ];

    protected $casts = [
        'retail' => 'integer',
        'sell_ikea' => 'float',
        'bulk' => 'integer',
        'progress' => 'boolean',
    ];
}
