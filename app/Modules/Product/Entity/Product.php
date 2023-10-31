<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Dimensions;
use App\Entity\Photo;
use App\Entity\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $code
 * @property string $description
 * @property string $short
 * @property int $main_category_id
 * @property string $dimensions_json
 * @property string $frequency_json
 * @property int $brand_id
 * @property float $current_rating
 * @property float $count_for_sell
 * @property bool $published //Опубликован
 * @property bool $only_offline //Только в магазине
 * @property bool $pre_order //Установка для всего магазина из опций, после каждый отдельно можно менять
 * @property bool $not_delivery
 * @property bool $not_local
 *
 * @property Tag[] $tags
 * @property Category[] $categories
 * @property Attribute[] $prod_attributes
 * @property Photo $photo
 * @property Photo[] $photos
 * @property Video[] $videos
 */
class Product extends Model
{

    //Константы тестировать - делать код и Индексировать Поле!!!
/*    const STATUS_DRAFT = 'Черновик';
    const STATUS_MODERATION = 'На модерации';
    const STATUS_APPROVED = 'Утвержден';
    const STATUS_PUBLISHED = 'Опубликован';

    const DELIVERY_NOT = 'Нет';
    const DELIVERY_LOCAL = 'В пределах региона';
    const DELIVERY_ALL = 'Транспортной компанией';
*/

    public Dimensions $dimensions;

    protected $attributes = [
        'short' => '',
        'description' => '',
        'dimensions_json' => '{}',
        'frequency_json' => '{}',
        'count_for_sell' => 0,
        'current_rating' => 0,
    ];

    protected $fillable = [
        'name',
        'slug',
        'code',
        'status',
        'description',
        'short',
        'main_category_id',
        'brand_id',
        'current_rating',
        'count_for_sell',
        'published',
        'only_offline ',
        'pre_order',
        'not_delivery',
        'not_local',
    ];

    protected $hidden = [

    ];

    //РЕГИСТРАТОРЫ
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        //Конфигурация


    }

    public static function register(string $name, string $code, int $main_category_id, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'code' => $code,
            'main_category_id' => $main_category_id,
        ]);

    }


    //SET и GET
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
/*
    public function setPublished(): void
    {
        $this->status = self::STATUS_PUBLISHED;
    }

    public function setModeration()
    {
        $this->status = self::STATUS_MODERATION;
    }

    public function setApproved()
    {
        $this->status = self::STATUS_APPROVED;
    }
*/
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return$this->name;
    }


    //ФУНЦИИ СОСТОЯНИЯ
    public function isVisible(): bool
    {/*
        if ($this->status != self::STATUS_PUBLISHED) return false;
        if ($this->count_for_sell == 0 and $this->sell_method == self::SELL_OFFLINE) return false;
        if ($this->sell_method == self::SELL_OFFLINE) return false;
        return true;*/
        return true;
    }


    //RELATIONSHIP

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'main_category_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products', 'product_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tags_products', 'product_id', 'tag_id');
    }

    public function prod_attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attributes_products', 'product_id', 'attribute_id');

    }

    public function photo()
    {
       return $this->morphOne(Photo::class, 'imageable')->where('sort', '=',0);
    }

    public function photos()
    {
       return $this->morphMany(Photo::class, 'imageable')->where('sort', '>',0);
    }

    public function videos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }


    public function isCategories($category_id): bool
    {
        foreach ($this->categories as $category){
            if ($category->id == (int)$category_id) return true;
        }
        return false;
    }



    public function isTag($tag_id): bool
    {
        foreach ($this->tags as $tag){
            if ($tag->id == (int)$tag_id) return true;
        }
        return false;
    }

    public function addCategory(Category $category)
    {
        //Проверка, если главной категории нет, то назначаем на главную

        //Проверка на совпадение с главной и второстепенными
    }


}
