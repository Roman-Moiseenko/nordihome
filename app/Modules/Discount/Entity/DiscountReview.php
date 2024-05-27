<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $review_id
 * @property int $order_id
 * @property float $amount
 * @property bool $used
 * @property Carbon $created_at
 * @property Carbon $used_at
// * @property Carbon $end_at
 */
class DiscountReview extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'amount',
        'used',
        'created_at',
        'used_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public static function new(float $amount, int $order_id): self
    {
        return self::make([
            'order_id' => $order_id,
            'amount' => $amount,
            'used' => false,
            'created_at' => now(),
            'used_at' => null
        ]);
    }

    public function isUsed(): bool
    {
        return $this->used == true;
    }

    public function used()
    {
        $this->used = true;
        $this->used_at = now();
        $this->save();
    }
}
