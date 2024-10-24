<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Entity\Photo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property Photo $image
 * @property Photo $icon
 * @property Category $parent
 * @property Category[] $children
 * @property Attribute[] $prod_attributes
 * @property Product[] $products
 *
 * @property int $_lft
 * @property int $_rgt
 */
class Category extends Model
{
    use NodeTrait, HasFactory;

    protected $fillable = [
      'name', 'parent_id', 'slug', 'title', 'description',
    ];
    public $timestamps = false;

    public static function register($name, $parent_id = null, $slug = '', $title = '', $description = ''): self
    {
        return self::create([
            'name' => $name,
            'parent_id' => $parent_id,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
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

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', '=','image')->withDefault();
    }

    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', '=', 'icon')->withDefault();
    }

    public function getImage(): string
    {
        if (empty($this->image->file)) {
            return '/images/default-catalog.jpg';
        } else {
            return $this->image->getUploadUrl();
        }
    }


    public function getIcon(): string
    {
        if (empty($this->icon->file)) {
            return '/images/default-catalog.png';
        } else {
            return $this->icon->getUploadUrl();
        }
    }

    public function getParentIdAll(): array
    {
        return Category::orderBy('id')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->pluck('id')->toArray();
    }

    public function getParentAll()
    {
        return Category::orderBy('_lft')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->get();
    }

    //TODO Вывести список своих атрибутов и родительских в show()
    public function all_attributes(): array
    {
        return array_merge($this->parent_attributes(), $this->prod_attributes()->getModels());
    }

    public function parent_attributes(): array
    {
        return $this->parent ? $this->parent->all_attributes() : [];
    }

    public function prod_attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attributes_categories', 'category_id', 'attribute_id');
    }

    public function products()
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
}
