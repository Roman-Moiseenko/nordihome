<?php

namespace App\Modules\Accounting\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property int $price
 * @property float $price_ikea
 * @property bool $progress
 */
class PriceOutbox extends Model
{
    public $timestamps = false;

    protected $table = 'price_outbox';

    protected $fillable = [
        'code',
        'price',
        'price_ikea',
        'progress',
    ];

    protected $casts = [
        'price' => 'integer',
        'price_ikea' => 'float',
        'progress' => 'boolean',
    ];
}
