<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property Category $category
 * @property Product[] $products
 */
class Equivalent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name'
    ];


    public static function register(string $name, int $category_id): self
    {
        return self::create([
            'name' => $name,
            'category_id' => $category_id,
        ]);
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'equivalents_products', 'equivalent_id', 'product_id');
    }

    public function getCrumbsCategory(): string
    {
        $result = '';
        $categories = $this->category->getParentAll();
        foreach ($categories as $category) {
            $result = $result . '/' . $category->name;
        }
        return $result;
    }

    public function isProduct($id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id === $id) return true;
        }
        return false;
    }
}
