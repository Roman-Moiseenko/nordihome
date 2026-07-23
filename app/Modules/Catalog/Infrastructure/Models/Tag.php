<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property boolean $is_main //Отображаются первые в списке тегов, возможно свое форматирование
 * @property Product[] $products
 */
class Tag extends Model
{
    use ImageField;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'slug',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
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
}
