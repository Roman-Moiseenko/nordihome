<?php

namespace App\Modules\Product\Entity;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property Meta $meta
 * @property Room $parent
 * @property Room[] $children
 * @property Attribute[] $prod_attributes
 * @property Product[] $products
 * @property string $svg
 * @property int $_lft
 * @property int $_rgt
 * @property bool $published
 */
class Room extends Model
{
    use NodeTrait, ImageField, IconField;
    protected $fillable = [
        'name', 'parent_id', 'slug', 'title', 'description',
        'published',
    ];
    public $timestamps = false;
    protected $attributes = [
        'meta' => '{}',
    ];
    protected $with = [
        'parent',
        'prod_attributes',
        'image',
        'icon',
    ];

    protected $casts = [
        'meta' => MetaCast::class,
        'published' => 'boolean',
    ];

    public static function register($name, $parent_id = null, $slug = ''): self
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
            'published' => false,
        ]);
    }

    public function getChildrenIdAll(): array
    {
        return Room::orderBy('id')->where('_lft', '>=', $this->_lft)->where('_rgt', '<=', $this->_rgt)->pluck('id')->toArray();

    }

    public function getParentIdAll(): array
    {
        return Room::orderBy('id')->where('_lft', '<=', $this->_lft)->where('_rgt', '>=', $this->_rgt)->pluck('id')->toArray();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'main_room_id', 'id');
    }

    public function allProducts(int $pagination = null)
    {

        $query = Product::where('published', true) //Опубликован AND
        ->where(function ($query) { //Категории входят в выбранную AND
            $query->whereHas('room', function ($query) {
                $query->where('_lft', '>=', $this->_lft)->where('_rgt', '<=', $this->_rgt);
            })->orWhereHas('rooms', function ($query) {
                $query->where('_lft', '>=', $this->_lft)->where('_rgt', '<=', $this->_rgt);
            });
        })->where(function ($query) { //Либо не содержит модификаций, либо Является базовым товаром для модификации
            $query->doesntHave('modification')->orWhere(function ($query){
                $query->has('main_modification')->whereHas('main_modification', function ($query) {
                    $query->where('not_sale', false);
                });
            });
        });

        return $query->get();



        //TODO Связанные таблицы ......
        $subCategories = []; //Получаем все подкатегории всех уровней вложенности
        //Ищем товары, у которых category_id IN $subCategories
        //Ищем по вторичным категориям в таблице CategoryAssignment
        //Возвращаем
        //if $pagination == null -> все товары
        // иначе через пагинацию

        return null;
    }
}
