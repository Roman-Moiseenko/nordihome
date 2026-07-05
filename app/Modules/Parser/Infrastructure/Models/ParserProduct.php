<?php

namespace App\Modules\Parser\Infrastructure\Models;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $slug
 * @property string $maker_id id в магазине Бренда
 * @property int $product_id
 * @property string $url
 * @property string $model используется для поиска товара в базе, совместно с maker_id
 * @property float $price_base
 * @property float $price_sell
 * @property string $short
 * @property string $description
 *
 * @property bool $fragile - Хрупкий, может влиять на стоимость доставки
 * @property bool $sanctioned - Санкционный, может влиять на стоимость доставки
 * @property bool $availability - Можно или нет возить под заказ
 *
 * @property array $composite список id товаров входящих в состав
 * @property array $quantity  Кол-во на складах
 * @property array $colors Цвета
 * @property array $packages Упаковки
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product $product
 * @property ParserCategory[] $categories
 */
class ParserProduct extends Model
{
    public $timestamps = true;
    protected $table = 'parser_products';
    protected $attributes = [
        'composite' => '{}',
        'quantity' => '{}',
        'colors' => '[]',
        'packages' => '[]',
    ];
    protected $casts = [
        'colors' => 'json',
        'composite' => 'json',
        'quantity' => 'json',
        'packages' => 'json',
        'price_base' => 'float',
        'price_sell' => 'float',
    ];

    protected $fillable = [
        'url',
        'product_id',
        'name',
        'code',
        'slug',
        'short',
        'description',
        'price_base',
        'price_sell',
        'fragile',
        'sanctioned',
        'availability',
        'composite',
        'colors',
        'packages',
    ];

    public static function register(string $url, int $product_id): self
    {
        return self::create([
            'url' => $url,
            'product_id' => $product_id,
        ]);
    }

    public function category():? ParserCategory
    {
        return $this->categories()->first();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ParserCategory::class,
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
        /** @var ParserCategory $category */
        $category = $this->categories()->first();
        return $category->brand->currency->first();
    }

}
