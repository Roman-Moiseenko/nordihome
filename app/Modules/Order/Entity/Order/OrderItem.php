<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Discount\Entity\Discount;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property int $first_quantity //удалить
 * @property bool $preorder //на предзаказ
 * @property int $supplier_document_id //Заказ поставщику
 * @property int $base_cost
 * @property int $sell_cost
 * @property int $discount_id
 * @property string $discount_type
 * @property array $options
 * @property bool $cancel
 * @property string $comment
 * @property int $reserve_id
 * @property Order $order
 * @property Reserve $reserve
 * @property Product $product
 * @property Discount $discount
 */
class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'quantity',
        'product_id',
        'base_cost',
        'sell_cost',
        'discount_id',
        'options',
        'cancel',
        'comment',
        'reserve_id',
        'discount_type',
        'preorder'
    ];

    protected $casts = [
        'options' => 'json',
        'base_cost' => 'float',
        'sell_cost' => 'float',
    ];

    public function changeQuantity(int $new_quantity)
    {
        if (is_null($this->first_quantity)) $this->first_quantity = $this->quantity;
        $this->quantity = $new_quantity;
        $this->save();
    }

    public function clearReserve()
    {
        $this->update(['reserve_id' => null]);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function reserve()
    {
        return $this->belongsTo(Reserve::class, 'reserve_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function discountName()
    {
        if (empty($this->discount_id)) return '';
        $discount = $this->discount_type::find($this->discount_id);
        return $this->discount_type::TYPE . ' ' . $discount->title;
    }
}
