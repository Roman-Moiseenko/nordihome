<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $currency_id
 * @property string $name
 * @property ArrivalDocument[] $arrivals
 * @property Product[] $products
 * @property Currency $currency
 */
class Distributor extends Model
{
    public $timestamps = false;
    public $fillable =[
        'name',
        'currency_id',
    ];

    public static function register(string $name, int $currency_id): self
    {
        return self::create([
            'name' => $name,
            'currency_id' => $currency_id,
            ]);
    }

    public function arrivals()
    {
        return $this->hasMany(ArrivalDocument::class, 'distributor_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'distributors_products',
            'distributor_id', 'product_id')->withPivot('cost');
    }

    public function getCostItem(int $product_id): float
    {
        foreach ($this->products as $product) {
            if ($product->id == $product_id) return $product->pivot->cost;
        }
        return 0.0;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
