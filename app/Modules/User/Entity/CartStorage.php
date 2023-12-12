<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property int $reserve_id //Связанная позиция в резерве
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $options_json //Опции товара [id1, id2, ...]
 * @property bool $check
 * @property Product $product
 * @property Reserve $reserve
 */

class CartStorage extends Model
{
    protected $attributes = ['check' => true];
    protected $table = 'cart_storage';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'reserve_id',
        'options_json',
        'check',
    ];

    protected $casts = ['check' => 'bool'];

    public static function register(int $user_id, int $product_id, int $quantity, ?int $reserve_id, array $options_json = []): self
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'reserve_id' => $reserve_id,
            'options_json' => json_encode($options_json),
        ]);
    }

    public function check(): void
    {
        $this->check = !$this->check;
        $this->save();
    }

    public function isCheck(): bool
    {
        return $this->check == true;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reserve()
    {

        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }

    public function clearReserve()
    {
        $this->update(['reserve_id' => null]);
    }
}
