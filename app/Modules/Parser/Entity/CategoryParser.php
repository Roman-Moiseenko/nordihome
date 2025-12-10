<?php

namespace App\Modules\Parser\Entity;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Base\Traits\PhotoField;
use App\Modules\Product\Entity\Brand;
use App\Modules\Product\Entity\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $url уникальный для исключения двойного парсинга
 * @property bool $active
 * @property int $parent_id
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
    use NodeTrait, ImageField;

    public $timestamps = false;
    protected $table = 'parser_categories';
    protected $fillable = [
        'name',
        'parent_id',
        'url',
        'active',
        'slug',
    ];

    public static function register(string $name, string $url, ?int $parent_id): self
    {
        $slug = Str::slug($name);
        if (!is_null(self::where('slug', $slug)->first())) {
            $slug .= '-' . $url;
        }
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'url' => $url,
            'active' => true,
            'slug' => $slug
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

    public function getParentAll()
    {
        return CategoryParser::orderBy('_lft')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->get();
    }

    public function getParentNames(): string
    {
        $categories = $this->getParentAll();
        $name = '';
        foreach ($categories as $category) {
            $name .= $category->name . "\\";
        }
        return $name;// .= $this->name;
    }


}
