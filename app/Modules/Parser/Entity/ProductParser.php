<?php

namespace App\Modules\Parser\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $maker_id id в магазине Бренда
 * @property int $product_id
 * @property int $currency_id Валюта парсера Установить в бренде
 * @property string $url
 * @property string $model
 * @property float $price_base
 * @property float $price_sell
 * @property string $ean
 * @property string $data
 * @property bool $fragile
 * @property bool $sanctioned
 * @property array $composite список id товаров входящих в состав
 * @property array $quantity  Кол-во на складах
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Product $product
 * @property CategoryParser[] $categories
 */
class ProductParser extends Model
{
    public $timestamps = false;
    protected $table = 'parser_products';


    protected $casts =[
        'data' => 'json',

    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoryParser::class,
            'parser_categories_products',
            'product_id', 'category_id'
        );
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
