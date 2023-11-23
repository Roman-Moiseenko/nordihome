<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Product\IWidgetHome;
use Illuminate\Database\Eloquent\Model;

/**
 *@property int $id
 *@property string $name
 *@property Product[] $products
 */
class Series extends Model implements IWidgetHome
{

    public $timestamps = false;
    protected $table = 'series';
    protected $fillable = ['name'];

    public static function register(string $name)
    {
        return self::create([
            'name' => $name,
        ]);
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'series_id', 'id');
    }

    public function isProduct($id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id === $id) return true;
        }
        return false;
    }

    public function ProductsForWidget()
    {
        return $this->products;
    }
}
