<?php

namespace App\Modules\Parser\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Parser\Entity\ParserProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $ikea_id уникальный для исключения двойного парсинга
 * @property bool $active
 * @property int $parent_id
 *
 * @property int $_lft
 * @property int $_rgt
 * @property ParserCategory $parent
 * @property ParserProduct[] $products
 * @property Category $category
 */
class ParserCategory extends Model
{
    use NodeTrait, ImageField;

    public $timestamps = false;
    protected $table = 'parser_categories';
    protected $fillable = [
        'name',
        'parent_id',
        'ikea_id',
        'active',
        'slug',
    ];
    protected $casts = [
        'active' => 'boolean',
    ];

    public static function register(string $name, string $ikea_id, ?int $parent_id): self
    {
        $slug = Str::slug($name);
        if (!is_null(self::where('slug', $slug)->first())) {
            $slug .= '-' . $ikea_id;
        }
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'ikea_id' => $ikea_id,
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


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ParserProduct::class, 'parser_categories_products', 'category_id', 'product_id');
    }

    public function getParentAll()
    {
        return ParserCategory::orderBy('_lft')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->get();
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

    public function allProducts(int $pagination = null)
    {
        return ParserProduct::where('availability', true) //Опубликован AND
        ->where(function ($query) { //Категории входят в выбранную AND
            $query->whereHas('categories', function ($query) {
                $query->where('_lft', '>=', $this->_lft)->where('_rgt', '<=', $this->_rgt);
            });
        })->get();
    }


}
