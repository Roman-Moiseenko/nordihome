<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Dimensions;
use App\Trait\PictureTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $code
 * @property string $description
 * @property string $short
 * @property int $main_photo_id
 * @property int $main_category_id
 * @property string $dimensions_json
 * @property string $frequency_json
 * @property int $brand_id
 * @property float $current_rating
 * @property float $count_for_sell
 * @property bool $published
 * @property string $type_sell
 */
class Product extends Model
{
    const ONLINE = 'online';
    const OFFLINE = 'offline';
    const ORDER = 'order';

    public Dimensions $dimensions;


    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'short',
    ];

    protected $hidden = [

    ];

    public static function register(string $name, string $code): self
    {
        $product = self::create([
            'name' => $name,
            'code' => $code
        ]);

        return $product;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    public function published(): void
    {
        //TODO Проверка на заполнение

        $this->published = true;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return$this->name;
    }

    public function isVisible(): bool
    {
        if (!$this->published) return false;
        if ($this->count_for_sell == 0 and $this->type_sell == self::OFFLINE) return false;
        if ($this->type_sell == self::OFFLINE) return false;
        return true;
    }

    public function brand()
    {
        if (empty($this->brand_id)) return new Brand();
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'main_category_id', 'id');
    }

    public function categories()
    {
        //TODO return $this->hasMany(CategoryAssignment::class, '','');
    }

    public function setBrand(Brand $brand)
    {
        $this->brand_id = $brand->id;
    }

    public function setMainCategory(Category $category)
    {
        $this->main_category_id = $category->id;
    }

    public function getMainCategory() //: Category
    {
        return $this->belongsTo(Category::class, 'main_category_id', 'id');
    }

    public function addCategory(Category $category)
    {
        //Проверка, если главной категории нет, то назначаем наглавную

        //Проверка на совпадение с главной и второстепенными
    }
}
