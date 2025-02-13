<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property Category $parent
 * @property Category[] $children
 * @property Attribute[] $prod_attributes
 * @property Product[] $products
 *
 * @property int $_lft
 * @property int $_rgt
 *
 * @property string $top_title
 * @property string $top_description
 * @property string $bottom_text
 * @property string $data
 */
class Category extends Model
{
    use NodeTrait, HasFactory, ImageField, IconField;

    protected $attributes = [
        'top_title' => '',
        'top_description' => '',
        'bottom_text' => '',
        'data' => '',
    ];

    protected $fillable = [
      'name', 'parent_id', 'slug', 'title', 'description',
    ];
    public $timestamps = false;

    public static function register($name, $parent_id = null, $slug = '', $title = '', $description = ''): self
    {
        $slug = empty($slug) ? Str::slug($name) : $slug;
        if (!empty(self::where('slug', $slug)->first())) {
            if (!is_null($parent_id)) {
                $parent = Category::find($parent_id);
                $slug .= '-' . $parent->slug;
            } else {
                $slug .= Str::random(4);
            }
        }
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'slug' => $slug,
            'title' => $title,
            'description' => $description,
        ]);
    }

    public function isId(int $id): bool
    {
        return $this->id == $id;
    }

    public function equilParent(Category $category): bool
    {
        if ($category->parent->id == null) return false;
        if ($this->parent->id == null) return false;
        return $category->parent->id == $this->parent->id;
    }

    public function isParent(Category $category): bool
    {
        if ($this->parent_id == null) return false;
        return $this->parent_id == $category->id;
    }

    public function getChildrenIdAll(): array
    {
        return Category::orderBy('id')->where('_lft', '>=', $this->_lft)->where('_rgt', '<=', $this->_rgt)->pluck('id')->toArray();

    }

    public function getParentIdAll(): array
    {
        return Category::orderBy('id')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->pluck('id')->toArray();
    }

    public function getParentAll()
    {
        return Category::orderBy('_lft')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->get();
    }

    /** @return Attribute[] */
    public function all_attributes(): array
    {
        return array_merge($this->parent_attributes(), $this->prod_attributes()->getModels());
    }

    public function parent_attributes(): array
    {
        return $this->parent ? $this->parent->all_attributes() : [];
    }

    public function prod_attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attributes_categories', 'category_id', 'attribute_id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'main_category_id', 'id');
    }

    public function allProducts(int $pagination = null):? Product
    {
        //TODO Связанные таблицы ......
        $subCategories = []; //Получаем все подкатегории всех уровней вложенности
        //Ищем товары, у которых category_id IN $subCategories
        //Ищем по вторичным категориям в таблице CategoryAssignment
        //Возвращаем
        //if $pagination == null -> все товары
        // иначе через пагинацию

        return null;
    }

    //META

    public function getTitle(): string
    {
        if (empty($this->title)){
            //TODO Генерация автоматического заголовка
            // при рефакторинге вынести в репозиторий для фронтенда
            return '';
        } else {
            return $this->title;
        }
    }

    public function getDescription(): string
    {
        if (empty($this->description)){
            //TODO Генерация автоматического описания
            // при рефакторинге вынести в репозиторий для фронтенда
            return '';
        } else {
            return $this->description;
        }
    }

    public function getSlug()
    {
        return '/catalog/' . $this->slug;
        /*
        if (isset($this->parent)) {
            $slug = $this->parent->getSlug();
        } else {
            $slug = '/categories';
        }
        return $slug . '/' . $this->slug;*/
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
