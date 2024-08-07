<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Base\Casts\DimensionsCast;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Entity\Video;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Shop\Parser\ProductParser;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $old_slug
 * @property string $code
 * @property string $code_search
 * @property string $description
 * @property string $short
 * @property int $main_category_id
 * // * @property string $dimensions_json
 * @property int $frequency
 * @property int $brand_id
 * @property float $current_rating
 * @property int $count_for_sell
 * @property float $current_price //Для быстрой сортировки
 * @property bool $published //Опубликован
 * @property bool $only_offline //Только в магазине
 * @property bool $pre_order //Установка для всего магазина из опций, после каждый отдельно можно менять
 * @property bool $not_delivery
 * @property bool $not_local
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 * @property int $series_id
 * @property Dimensions $dimensions
 * @property bool $priority
 *
 * @property Tag[] $tags
 * @property Category $category
 * @property Category[] $categories
 * @property Attribute[] $prod_attributes
 * @property \App\Modules\Base\Entity\Photo $photo
 * @property \App\Modules\Base\Entity\Photo $photo_next
 * @property \App\Modules\Base\Entity\Photo[] $photos
 * @property Video[] $videos
 *
 * @property ProductPriceRetail[] $prices
 * @property ProductPriceCost[] $pricesCost
 * @property ProductPriceRetail[] $pricesRetail
 * @property ProductPriceBulk[] $pricesBulk
 * @property ProductPriceSpecial[] $pricesSpecial
 * @property ProductPriceMin[] $pricesMin
 * @property ProductPricePre[] $pricesPre
 * @property Promotion[] $promotions Все акции в которых есть товар
 * @property Equivalent $equivalent
 * @property EquivalentProduct $equivalent_product
 * @property Product[] $related
 * @property Product[] $bonus
 * @property Brand $brand
 * @property Group[] $groups
 * @property Modification $modification
 * @property ModificationProduct $modification_product
 * @property Series $series
 *
 * @property CartStorage[] $cartStorages
 * @property CartCookie[] $cartCookies
 * @property Wish[] $wishes
 * @property OrderReserve[] $reserves
 * @property StorageItem[] $storageItems
 * @property OrderItem[] $orderItems
 * @property Review[] $reviews
 * @property Review[] $reviewsAll
 * @property ProductParser $parser
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

    //public \App\Modules\Base\Entity\Dimensions $dimensions;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'dimensions' => DimensionsCast::class,
    ];

    protected $attributes = [
        'short' => '',
        'dimensions' => '{}',
        'description' => '',
        'frequency' => self::FREQUENCY_NOT,
        'count_for_sell' => 0,
        'current_rating' => 0,
        'published' => false,
        'pre_order' => true,
        'series_id' => null,
        'published_at' => null,
        'priority' => false,
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
        'code_search',
        'series_id',
        //'published_at',
        'priority',
        // 'description',
    ];

    protected $hidden = [

    ];

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    //РЕГИСТРАТОРЫ

    /**
     * Регистрация товара, с учетом дублей по имени - добавляем номер
     * Для дублирования артикула - вызываем исключение
     * @param string $name
     * @param string $code
     * @param int $main_category_id
     * @param string $slug
     * @param array $arguments
     * @return static
     */
    public static function register(string $name, string $code, int $main_category_id, string $slug = '', array $arguments = []): self
    {
        if (!empty(Product::where('code', '=', $code)->first()))
            throw new \DomainException('Дублирование. Товар с артикулом ' . $code . ' уже существует');

        if (!empty(Product::where('name', $name)->first())) $name .= ' ' . $code;

        $slug = empty($slug) ? Str::slug($name) : $slug;
        if (Product::where('slug', $slug)->first() != null) $slug = Str::uuid();

        $code_search = str_replace(['-', ',', '.', '_', ':'], '', $code);
        $data = [
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'code' => $code,
            'code_search' => $code_search,
            'main_category_id' => $main_category_id,
        ];

        return self::create(array_merge($data, $arguments));
    }

    //ФУНЦИИ СОСТОЯНИЯ
    public function isNew(): bool
    {
        if ($this->published_at == null) return false;
        if ($this->published_at->gte(now()->subMonth())) return true;
        return false;
    }

    #[Deprecated]
    public function isVisible(): bool
    {
        return true;
    }

    public function isSeries($id): bool
    {
        if ($this->series_id == null) return false;
        return $this->series_id == $id;
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
        foreach ($this->categories as $category) {
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
        foreach ($this->tags as $tag) {
            if ($tag->id == (int)$tag_id) return true;
        }
        return false;
    }

    public function isWish(int $user_id)
    {
        $wish = $this->wishes()->where('user_id', $user_id)->first();
        return !empty($wish);
    }

    public function isBonus(int $product_id): bool
    {
        foreach ($this->bonus as $bonus) {
            if ($bonus->id == $product_id) return true;
        }
        return false;
    }

    public function isRelated(int $product_id): bool
    {
        foreach ($this->related as $related) {
            if ($related->id == $product_id) return true;
        }
        return false;
    }

    //*** SET-....
    //SET и GET
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setPriority(bool $priority): void
    {
        $this->priority = $priority;
        $this->save();
    }

    #[Deprecated]
    public function setPrice(float $price): void
    {
        if ($this->getPriceRetail() === $price) return;
        $this->current_price = $price;
        $this->save();
        $this->prices()->create(
            [
                'value' => $price,
                'founded' => 'In Shop',
            ]
        );
        $this->refresh();
    }

    public function setPublished(): void
    {
        $this->published = true;
        if (is_null($this->published_at)) $this->published_at = now();
        $this->save();
    }

    public function setDraft(): void
    {
        $this->published = false;
        $this->save();
    }
    /*
            public function setModeration()
            {
                $this->status = self::STATUS_MODERATION;
            }

            public function setApproved()
            {
                $this->status = self::STATUS_APPROVED;
            }
        */

    //*** GET-....
    public function getSlug(): string
    {
        return $this->slug;
    }

    //*** ЦЕНЫ

    /**
     * @return float Последняя назначенная цена с учетом если цены не назначены
     */
    public function getLastPrice(): float
    {
        //TODO Привязка к Аутентификации!!!

        if (!is_null($user = Auth::guard('user')->user())) {
            /** @var User $user */
            if ($user->isBulk()) return $this->getPriceBulk(); //Оптовый клиент
            if ($user->isSpecial()) return $this->getPriceSpecial(); //Спец Клиент
        }
        return $this->getPriceRetail();
    }

    /**
     * Последняя закупочная цена, через Поставщиков
     * @return float
     */
    public function getPriceCost(bool $previous = false): float
    {
        if ($this->pricesCost()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceCost $model */
            $model = $this->pricesCost()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesCost()->skip(0)->first();
        }
        return $model->value;
    }

    public function getPriceRetail(bool $previous = false): float
    {
        if ($this->pricesRetail()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceRetail $model */
            $model = $this->pricesRetail()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesRetail()->skip(0)->first();
        }
        return $model->value;
    }

    public function getPriceBulk(bool $previous = false): float
    {
        if ($this->pricesBulk()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceBulk $model */
            $model = $this->pricesBulk()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesBulk()->skip(0)->first();
        }
        return $model->value;
    }

    public function getPriceSpecial(bool $previous = false): float
    {
        if ($this->pricesSpecial()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceSpecial $model */
            $model = $this->pricesSpecial()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesSpecial()->skip(0)->first();
        }
        return $model->value;
    }

    public function getPriceMin(bool $previous = false): float
    {
        if ($this->pricesMin()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceMin $model */
            $model = $this->pricesMin()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesMin()->skip(0)->first();
        }
        return $model->value;
    }

    /**
     * Цена на предзаказ, если цена не определена, возвращаем текущую
     * @param bool $previous
     * @return float
     */
    public function getPricePre(bool $previous = false): float
    {
        if ($this->pricesPre()->count() == 0) return 0;
        if ($previous == true) {
            /** @var ProductPriceMin $model */
            $model = $this->pricesPre()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesPre()->skip(0)->first();
        }
        return $model->value;
    }

    //*** КОЛ_ВО
    public function getReserveCount(): int
    {
        $result = 0;
        foreach ($this->storageItems as $storageItem) {
            $result += $storageItem->getQuantityReserve();
        }
        return $result;
    }

    #[Deprecated]
    public function setCountSell(int $count): void
    {
        //$this->count_for_sell = $count;
        //$this->save();
    }


    /**
     * Кол-во доступное для продажи по всем точкам, за минусом резерва
     * @return int
     */
    #[Pure]
    public function getCountSell(): int
    {
        //return $this->count_for_sell;
        return $this->getQuantity() - $this->getQuantityReserve();
    }

    /**
     * Кол-во товара на складах
     * @param int|null $storage_id
     * @return int
     */
    public function getQuantity(int $storage_id = null): int
    {
        $quantity = 0;
        foreach ($this->storageItems as $storageItem) {
            if (is_null($storage_id)) {
                $quantity += $storageItem->quantity;
            } else {
                if ($storageItem->storage_id == $storage_id) return $storageItem->quantity;
            }
        }
        return $quantity;
    }

    //TODO Переименовать
    #[Pure] public function getQuantityReserve(): int
    {
        $quantity = 0;
        foreach ($this->storageItems as $storageItem) {
            $quantity += $storageItem->getQuantityReserve();
        }
        return $quantity;
    }

    /**
     * Предыдущая цена товара (учитывается случаи когда всего цен менее 2
     * @return float
     */
    public function getPreviousPrice(): float
    {
        $count = $this->prices()->count();
        if ($count == 0) return 0;
        if ($count == 1) return $this->getLastPrice();
        /** @var ProductPriceRetail $model */
        $model = $this->prices()->skip(1)->first();
        return $model->value;
    }

    public function getName(): string
    {
        return $this->name;
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


    /**
     * Хранилища, где данный товар есть в наличии
     * @return Storage[]
     */
    public function getStorages(): array
    {

        return Storage::whereHas('items', function ($q) {
            $q->where('product_id', $this->id);
        })->getModels();
        //return $this->hasMany(StorageItem::class, 'product_id', 'id')-;
    }

    /**
     * @return StorageItem[]
     */
    public function getStorageItems(): array
    {
        return StorageItem::where('product_id', $this->id)->getModels();
    }

    public function getImage(string $thumb = ''): string
    {
        if (empty($this->photo->file)) {
            return '/images/no-image.jpg';
        } else {
            if ($thumb == '') {
                return $this->photo->getUploadUrl();
            } else {
                return $this->photo->getThumbUrl($thumb);
            }
        }
    }

    /**
     * Действующая акция или null
     * @return Promotion|null
     */
    public function promotion(): ?Promotion
    {
        foreach ($this->promotions as $promotion) {
            if ($promotion->isStarted()) return $promotion;
        }
        return null;
    }


    //*** RELATIONS

    public function parser()
    {
        return $this->hasOne(ProductParser::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id')->where('status', Review::STATUS_PUBLISHED)->orderByDesc('created_at');
    }

    public function reviewsAll()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    //ТОВАРНЫЙ УЧЕТ

    public function storageItems()
    {
        return $this->hasMany(StorageItem::class, 'product_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotions_products',
            'product_id', 'promotion_id')->withPivot('price');
    }

    public function cartStorages()
    {
        return $this->hasMany(CartStorage::class, 'product_id', 'id');
    }

    public function cartCookies()
    {
        return $this->hasMany(CartCookie::class, 'product_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(ProductPriceRetail::class, 'product_id', 'id')->orderByDesc('id');
    }

    //ЦЕНЫ ****************
    public function pricesCost()
    {
        return $this->hasMany(ProductPriceCost::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesRetail()
    {
        return $this->hasMany(ProductPriceRetail::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesBulk()
    {
        return $this->hasMany(ProductPriceBulk::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesSpecial()
    {
        return $this->hasMany(ProductPriceSpecial::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesMin()
    {
        return $this->hasMany(ProductPriceMin::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesPre()
    {
        return $this->hasMany(ProductPricePre::class, 'product_id', 'id')->orderByDesc('id');
    }

    //ХАРАКТЕРИСТИКИ ТОВАРА
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

    public function bonus() //Для attach и detach
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
        return $this->morphOne(Photo::class, 'imageable')->where('sort', '=', 0);
    }

    public function photos()
    {
        return $this->morphMany(\App\Modules\Base\Entity\Photo::class, 'imageable')->orderBy('sort')->orderBy('id');//->where('sort', '>',0);
    }

    public function photo_next()
    {
        return $this->photos()->where('sort', '>', 0)->first();
    }

    public function videos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }

    public function modification_product()
    {
        return $this->hasOne(ModificationProduct::class, 'product_id', 'id');
    }

    public function modification()
    {
        return $this->hasOneThrough(
            Modification::class,
            ModificationProduct::class,
            'product_id',
            'id',
            'id',
            'modification_id'
        );
        // return $this->belongsTo(Modification::class, 'product_id', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_products', 'product_id', 'group_id');
    }

    public function equivalent_product()
    {
        return $this->hasOne(EquivalentProduct::class, 'product_id', 'id');
    }

    public function equivalent()
    {
        return $this->belongsTo(Equivalent::class, 'product_id', 'id');
    }

    public function wishes()
    {
        return $this->hasMany(Wish::class, 'product_id', 'id');
    }



    /*
        public static function boot()
        {
            parent::boot();
            self::saving(function (Product $product) {
               // $product->dimensions_json = $product->dimensions->toSave();
               // if ($product->getCountSell() < 0) throw new \DomainException('Кол-во товаров должно быть >= 0');
            });

            self::retrieved(function (Product $product) {
               // $product->dimensions = \App\Modules\Base\Entity\Dimensions::load($product->dimensions_json);
            });
        }
    */
    /**
     * Имеется действующая акция
     * @return bool
     */
    public function hasPromotion(): bool
    {
        return !is_null($this->promotion());
    }


    public function countReviews(): string
    {
        //if ($this->reviews)
        $text = $this->reviews()->count() . ' отзыв';
        $count = $this->reviews()->count();
        if ($count == 1 || ($count > 20 && $count % 10 == 1)) {
            return $text;
        }

        if (in_array($count, [2, 3, 4]) || (in_array($count & 10, [2, 3, 4]) && $count % 100 > 20)) {
            return $text . 'а';
        }
        if (($count > 4 && $count < 19) || in_array($count % 10, [5, 6, 7, 8, 9, 0])) {
            return $text . 'ов';
        }
        //TODO Сделать отзывы
        return '0 отзывов';
    }

    public function updateReview()
    {
        $this->current_rating = 0;
        if ($this->reviews()->count() == 0) {
            $this->save();
            return;
        }
        foreach ($this->reviews as $review) {
            $this->current_rating += $review->rating;
        }
        $this->current_rating = ($this->current_rating) / $this->reviews()->count();
        $this->save();
    }

    public function scopeWhereCode($query, $code)
    {
        return $query->where(function ($q) use ($code) {
            $q->where('code', $code)->orWhere('code_search', $code);
        });
    }

    /**
     * Проверяет участвует ли данный атрибут в Модификации
     * @param int $attribute_id
     * @return bool
     */
    public function AttributeIsModification(int $attribute_id): bool
    {
        if (is_null($this->modification)) return false;
        foreach ($this->modification->prod_attributes as $attribute) {
            if ($attribute->id == $attribute_id) {
                return true;
            }
        }
        return false;
    }

}
