<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Dimensions;
use App\Entity\Photo;
use App\Entity\Video;
use App\Modules\Product\Repository\CategoryRepository;
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
 * @property int $frequency
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
 * @property Category $category
 * @property Category[] $categories
 * @property Attribute[] $prod_attributes
 * @property Photo $photo
 * @property Photo[] $photos
 * @property Video[] $videos
 * @property ProductPricing[] $pricing
 * @property ProductPricing $lastPrice
 * @property Equivalent $equivalent
 * @property Product[] $related
 * @property Product[] $bonus
 * @property Group[] $groups
 * @property Modification $modification
 */
class Product extends Model
{

    const FREQUENCY_MAJOR = 101;
    const FREQUENCY_AVERAGE = 102;
    const FREQUENCY_SMALL = 103;
    const FREQUENCY_PERIOD = 104;
    const FREQUENCY_NOT = 105;

    const FREQUENCIES = [
        self::FREQUENCY_MAJOR => 'Крупная покупка (от 3 лет)',
        self::FREQUENCY_AVERAGE => 'Средняя покупка (1-3 года)',
        self::FREQUENCY_SMALL => 'Ходовой товар, с небольшим сроком пользования',
        self::FREQUENCY_PERIOD => 'Расходный товар',
        self::FREQUENCY_NOT => 'Нет',
    ];


    public Dimensions $dimensions;

    protected $attributes = [
        'short' => '',
        'description' => '',
        'dimensions_json' => '{}',
        'frequency' => self::FREQUENCY_NOT,
        'count_for_sell' => 0,
        'current_rating' => 0,
        'published' => false,
        'pre_order' => false,
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
        'frequency',
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
        $this->dimensions = new Dimensions();
        //Конфигурация

    }

    public static function register(string $name, string $code, int $main_category_id, string $slug = ''): self
    {
        //$dublicat = Product::where('name', '=', $name)->get();
        if (!is_null(Product::where('name', '=', $name)->first())) throw new \DomainException('Дублирование. Товар ' . $name . ' уже существует');
        if (!is_null(Product::where('code', '=', $code)->first())) throw new \DomainException('Дублирование. Товар с артикулом ' . $code . ' уже существует');
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

    public function setPrice(float $price): void
    {
        if (!is_null($this->lastPrice) && $this->lastPrice->value === $price) return;

        $this->pricing()->create(
            [
                'value' => $price,
                'founded' => 'In Shop',
                ]
        );
        $this->refresh();
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

    public function getLastPrice(): float
    {
        if (is_null($this->lastPrice)) return 0;
        return $this->lastPrice->value;
    }

    public function getPreviousPrice(): float
    {
        if (empty($this->pricing)) return 0;
        $count = count($this->pricing);
        if ($count == 1) return $this->getLastPrice();
        return $this->pricing[$count - 2]->value;
    }

    public function getName(): string
    {
        return$this->name;
    }

    public function Value(int $attribute_id)
    {
        foreach ($this->prod_attributes as $attribute) {
            if ($attribute->id === $attribute_id) return $attribute->Value();
        }
        return null;
    }

    public function getProdAttribute(int $id_attr): ?Attribute
    {
        //if (empty())
        foreach ($this->prod_attributes as $attribute) {
            if ($attribute->id === $id_attr) return $attribute;
        }
        return null;
    }

    /** @return Attribute[] */
    public function getPossibleAttribute(): array
    {
        //TODO Вынести - AttributeRepository->getPossibleForProduct($id)
        // и переделать на SQL запрос через JOIN Attribute->AttributeCategory->CategoryProduct(product_id = $id)
        $result = [];
        $_array_all = $this->category->getParentIdAll();

        foreach ($this->categories as $category) {
            $_array_all = array_merge($_array_all, $category->getParentIdAll());
        }
        $_array_all = array_unique($_array_all);
        $categories = Category::orderBy('id')->whereIn('id', $_array_all)->get();
        /** @var Category $category */
        foreach ($categories as $category) {
            foreach ($category->prod_attributes as $attribute) {
                if (!isset($result[$attribute->id])) {
                    $result[$attribute->id] = $attribute;
                }
            }
        }
        return $result;
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

    public function isLocal(): bool
    {
        return !$this->not_local;
    }

    public function isDelivery(): bool
    {
        return !$this->not_delivery;
    }

    public function isCategories($category_id): bool
    {
        foreach ($this->categories as $category){
            if ($category->id == (int)$category_id) return true;
        }
        return false;
    }

    public function isPublished(): bool
    {
        return $this->published == true;
    }

    public function isTag($tag_id): bool
    {
        foreach ($this->tags as $tag){
            if ($tag->id == (int)$tag_id) return true;
        }
        return false;
    }

    //RELATIONSHIP

    public function modification()
    {
        /** @var ModificationProduct $mp */
        $mp = ModificationProduct::where('product_id', '=', $this->id)->first();
        if (empty($mp)) return null;
        return $mp->modification();

    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_products', 'product_id', 'group_id');
    }

    public function equivalent()
    {
        //TODO сделать через hasOneThrough
        $eq_prod = EquivalentProduct::where('product_id', '=', $this->id)->first();
        if (empty($eq_prod)) return null;
        return $eq_prod->equivalent();

        //$eq_prod = $this->hasOne(EquivalentProduct::class, 'product_id', 'id');

        //return $e_p->equivalent();
        //return $this->hasOneThrough(Equivalent::class, EquivalentProduct::class, 'product_id', 'equivalent_id', 'id', 'id');
    }

    public function pricing()
    {
        return $this->hasMany(ProductPricing::class, 'product_id', 'id')->orderByDesc('created_at');
    }

    public function lastPrice()
    {
        return $this->hasOne(ProductPricing::class, 'product_id', 'id')->latestOfMany();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'main_category_id', 'id');
    }

    public function related()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_id');
    }

    public function bonus()
    {
        return $this->belongsToMany(Product::class, 'bonus_products', 'product_id', 'bonus_id')->withPivot('discount');
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
        return $this->belongsToMany(
            Attribute::class, 'attributes_products',
            'product_id', 'attribute_id')->withPivot('value');
    }

    public function photo()
    {
       return $this->morphOne(Photo::class, 'imageable')->where('sort', '=',0);
    }

    public function photos()
    {
       return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');//->where('sort', '>',0);
    }

    public function videos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    public function getImage()
    {
        if (empty($this->photo->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->photo->getUploadUrl();
        }
    }


    public function addCategory(Category $category)
    {
        //Проверка, если главной категории нет, то назначаем на главную

        //Проверка на совпадение с главной и второстепенными
    }

    public static function boot()
    {
        parent::boot();
        self::saving(function (Product $product) {
            $product->dimensions_json = $product->dimensions->toSave();
        });

        self::retrieved(function (Product $product) {
            $product->dimensions =  Dimensions::load($product->dimensions_json);
        });
    }

}
