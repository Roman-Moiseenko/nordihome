<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $reserve_at
 * @property Product $product
 * @property User $user
 * @property CartStorage $cart
 */

class Reserve extends Model
{
    public $timestamps = false;
    protected $table = 'reserve';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'created_at',
        'reserve_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'reserve_at' => 'datetime',
    ];

    public static function register(int $product_id, int $quantity, int $user_id, int $hours = 1): self
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'created_at' => now(),
            'reserve_at' => now()->addHours($hours),
        ]);
    }

    public function updateReserve(int $quantity, int $hours = 1)
    {
        $this->update([
            'quantity' => $this->quantity + $quantity,
            'reserve_at' => now()->addHours($hours),
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cart()
    {
        return $this->hasOne(CartStorage::class, 'reserve_id', 'id');
    }
}
