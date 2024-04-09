<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $quantity
 * @property int $storage_id
 * @property Carbon $created_at
 * @property Carbon $reserve_at
 * @property Product $product
 * @property User $user
 * @property CartStorage $cart
 * @property OrderItem $orderItem
 * @property string $type
 */

class Reserve extends Model
{

    const TYPE_CART = 'cart';
    const TYPE_ORDER = 'order';
    const TYPE_SHOP = 'shop';
    const TYPE_MANUAL = 'manual';

    public $timestamps = false;
    protected $table = 'reserve';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'created_at',
        'reserve_at',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'reserve_at' => 'datetime',
    ];

    public static function register(int $product_id, int $quantity, int $user_id, int $minutes, string $type): self
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'created_at' => now(),
            'reserve_at' => now()->addMinutes($minutes),
            'type' => $type //Корзина cart, Заказ order, Магазин shop
        ]);
    }

    public function updateReserve(int $quantity, int $minutes = 1)
    {
        $this->update([
            'quantity' => $this->quantity + $quantity,
            'reserve_at' => now()->addMinutes($minutes),
        ]);
    }

    public function setStorage(int $storage_id)
    {
        $this->storage_id = $storage_id;
        $this->save();
    }

    public function sub(int $quantity)
    {
        if ($this->quantity < $quantity) throw new \DomainException('Превышение ол-ва товара в резерве');
        $this->quantity -= $quantity;
        $this->save();
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

    public function orderItem()
    {
        return $this->hasOne(OrderItem::class, 'reserve_id', 'id');
    }

    public function add(int $quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

}
