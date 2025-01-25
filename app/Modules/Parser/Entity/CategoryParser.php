<?php

namespace App\Modules\Parser\Entity;

use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $url уникальный для исключения двойного парсинга
 * @property bool $active
 * @property int $parent_id
 * @property int $category_id
 * @property int $brand_id Привязка к бренду
 *
 * @property int $_lft
 * @property int $_rgt
 * @property Brand $brand
 * @property CategoryParser $parent
 * @property ProductParser[] $products
 * @property Category $category
 */
class CategoryParser extends Model
{
    use NodeTrait, HasFactory;

    public $timestamps = false;
    protected $table = 'parser_categories';
    protected $fillable = [
        'name',
        'parent_id',
        'url',
        'active',
    ];

    public static function register(string $name, string $url, ?int $parent_id): self
    {
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'url' => $url,
            'active' => true,
        ]);
    }
    public function draft(): void
    {
        $this->active = false;
        $this->save();
    }

    public function active(): void
    {
        $this->active = true;
        $this->save();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ProductParser::class, 'parser_categories_products', 'category_id', 'product_id');
    }
}
