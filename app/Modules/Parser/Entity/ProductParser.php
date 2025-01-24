<?php

namespace App\Modules\Parser\Entity;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $maker_id id в магазине Бренда
 * @property int $product_id
 * @property string $url
 * @property string $model используется для поиска товара в базе, совместно с maker_id
 * @property float $price_base
 * @property float $price_sell
 *
 * @property bool $fragile - Хрупкий, может влиять на стоимость доставки
 * @property bool $sanctioned - Санкционный, может влиять на стоимость доставки
 * @property bool $availability - Можно или нет возить под заказ
 *
 * @property array $composite список id товаров входящих в состав
 * @property array $quantity  Кол-во на складах
 * @property array $data Структурированные данные (для NB - варианты)
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product $product
 * @property CategoryParser[] $categories
 */
class ProductParser extends Model
{
    public $timestamps = false;
    protected $table = 'parser_products';

    protected $casts =[
        'data' => 'json',
        'composite' => 'json',
        'quantity' => 'json',

    ];

    protected $fillable = [
        'name',
        'url',
        'product_id',
    ];

    public static function register(string $name, string $url, int $product_id, )
    {
        return self::create([
            'name' => $name,
            'url' => $url,
            'product_id' => $product_id,
        ]);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoryParser::class,
            'parser_categories_products',
            'product_id', 'category_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function currency(): Currency
    {
        /** @var CategoryParser $category */
        $category = $this->categories()->first();
        return $category->brand->currency->first();
    }

}
