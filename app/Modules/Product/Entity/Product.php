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
 * @property string $status
 * @property string $sell_method
 */
class Product extends Model
{
    const SELL_ONLINE = 'online';
    const SELL_OFFLINE = 'offline';
    const SELL_ORDER = 'order';

    const STATUS_DRAFT = 'Черновик';
    const STATUS_MODERATION = 'На модерации';
    const STATUS_APPROVED = 'Утвержден';
    const STATUS_PUBLISHED = 'Опубликован';


    public Dimensions $dimensions;

    protected $attributes = [
        'short' => '',
        'description' => '',
        'delayed' => false,
        'dimensions_json' => '{}',
        'frequency_json' => '{}',
        'sell_method' => self::SELL_ONLINE,
        'status' => self::STATUS_DRAFT,
        'count_for_sell' => 0,
    ];

    protected $fillable = [
        'name',
        'slug',
        'code',
        'status',
        'description',
        'short',
        'sell_method',
    ];

    protected $hidden = [

    ];

    //РЕГИСТРАТОРЫ

    public static function register(string $name, string $code, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'code' => $code,
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
    {
        if ($this->status != self::STATUS_PUBLISHED) return false;
        if ($this->count_for_sell == 0 and $this->type_sell == self::SELL_OFFLINE) return false;
        if ($this->type_sell == self::SELL_OFFLINE) return false;
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

    public function photo()
    {
       return $this->morphOne(Photo::class, 'imageable')->where('type', 'main');
    }

    public function photos()
    {
       return $this->morphMany(Photo::class, 'imageable');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'product_id', 'id');
    }

    public function categories()
    {
        //TODO return $this->hasMany(CategoryAssignment::class, '','');
    }


    public function addCategory(Category $category)
    {
        //Проверка, если главной категории нет, то назначаем наглавную

        //Проверка на совпадение с главной и второстепенными
    }


}
