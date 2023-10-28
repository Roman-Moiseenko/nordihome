<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Product[] $products
 */
class Tag extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'slug',
    ];

    public static function register(string $name): self
    {
        return static::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tags_products', 'tag_id', 'product_id');
    }

    public function getSlug(): string
    {
        return '/tag/' . $this->slug;
    }

}
