<?php

namespace App\Modules\Accounting\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $reserve
 * @property Carbon $updated_at
 */
class Stock extends Model
{

    public $timestamps = false;
    protected $table = 'stock';
    protected $casts = [
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'product_id',
        'quantity',
        'reserve',
        'updated_at'
    ];
}
