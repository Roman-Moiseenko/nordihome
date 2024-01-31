<?php


namespace App\Modules\Shop\Parser;


use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductParser
 * @package App\Modules\Shop\Parser
 * @property int $id
 * @property int $product_id
 * @property bool $order //Можно ли под заказ
 * @property int $packs
 * @property array $composite //список id товаров входящих в состав
 * @property float $price //Цена в злотах
 * @property array $quantity
 * @property string $link
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Product $product
 */
class ProductParser extends Model
{
    protected $table = 'product_parser';
    protected $casts = [
        'composite' => 'json',
        'quantity' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'product_id',
        'packs',
        'composite',
        'price',
        'order',
        'quantity',
        'link'
    ];

    public static function register(int $product_id, int $packs, float $price, array $composite, string $link): self
    {
        return self::create([
            'product_id' => $product_id,
            'packs' => $packs,
            'composite' => $composite,
            'price' => $price,
            'quantity' => [],
            'order' => true,
            'link' => $link,
        ]);
    }

    public function setQuantity(array $quantity)
    {
        $this->update(['quantity' => $quantity]);
    }

    public function setCost(float $price)
    {
        $this->update(['price' => $price]);
    }

    public function block()
    {
        $this->update(['order' => false]);
    }

    public function unblock()
    {
        $this->update(['order' => true]);
    }

    public function isBlock(): bool
    {
        return $this->order == false;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function composite(): string
    {
        $result = [];
        if (!empty($this->composite)) {
            foreach ($this->composite as $item) {
                $result[] = $item['code'] . ' - ' . $item['quantity'] . ' шт.';
            }
        }
        return empty($result) ? '' : implode('<br>', $result);
    }

    public function quantity(): string
    {
        $result = '';
        foreach ($this->quantity as $store => $quantity) {
            $result .= ParserService::STORES[(int)$store] . '-' . $quantity . ' ';
        }
        return $result;
    }
}
