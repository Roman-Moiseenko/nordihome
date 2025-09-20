<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $published
 * @property string $description
 * @property Product[] $products
// * @property Promotion[] $promotions
 */
class Group extends Model
{
    use ImageField;

    public $timestamps = false;

    protected $attributes = [
        'published' => false,
    ];

    protected $fillable = [
        'name', 'description', 'slug', 'published'
    ];

    public static function register(string $name, string $description = '', string $slug = '', bool $published = false): static
    {
        return static::create([
            'name' => $name,
            'description' => $description,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'published' => $published,
        ]);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'groups_products', 'group_id', 'product_id');
    }


    public function isProduct(int $id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $id) return true;
        }
        return false;
    }

}
