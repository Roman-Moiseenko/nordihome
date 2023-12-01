<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $product_id
 * @property Carbon $created_at
 * @property Product $product
 * @property User $user
 */
class Wish extends Model
{
    protected $table = 'wish';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'created_at',
    ];
    public static function register(int $user_id, int $product_id): self
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'created_at' => now(),
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
    //TODO Сделать избранное
}
