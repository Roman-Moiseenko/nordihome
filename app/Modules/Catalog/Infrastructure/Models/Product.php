<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Accounting\Entity\BalanceProduct;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Infrastructure\Models\ProductPrice;
use App\Modules\Base\Casts\DimensionsCast;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Video;
use App\Modules\Cabinet\Infrastructure\Models\Wish;
use App\Modules\Cart\Infrastructure\Models\CartCookie;
use App\Modules\Cart\Infrastructure\Models\CartStorage;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $name
 * @property string $name_print Название для печати
 * @property string $slug
 * @property string $old_slug
 * @property string $code Артикул
 * @property string $code_search Артикул для поиска, без разделительных символов
 * @property string $description
 * @property string $short короткое описание
 * @property string $care уход за товаром (описание тегами)
 * @property string $comment Комментарий, для специалистов. Клиентам не видно
 *
 * @property int $main_category_id
 * @property int $brand_id
 * @property int $series_id серия
 * @property float $current_rating рейтинг по отзывам
 *
 * @property float $current_price *** неиспользуется, Для быстрой сортировки ??
 * @property bool $only_offline ** неиспользуется, Только в магазине
 *
 * @property bool $published  Опубликован
 * @property bool $pre_order Установка для всего магазина из опций, после каждый отдельно можно менять
 * @property bool $delivery Доставка ТК
 * @property bool $local Доставка по региону
 * @property bool $priority Приоритетный показ
 * @property bool $not_sale Снят с продажи
 * @property bool $only_on_order Только под заказ
 *
 * @property Dimensions $dimensions Габариты товара (+ вес),
 * @property array $packages Упаковки + вес + кол-во пачек
 * @property string $model Модель товара, не для всех товаров, опционо
 * @property string $barcode Штрих-код
 * @property bool $fractional  Дробное кол-во при учете
 * @property bool $hide_price Не указывать в прайс листах
 * @property string $complexity Сложность упаковки
 *
 * @property int $country_id Страна
 * @property int $measuring_id Ед.измерения
 * @property int $marking_type_id Вид продукции
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 *
 * @property Country $country
 * @property Measuring $measuring
 * @property MarkingType $markingType
 *
 * @property Tag[] $tags
 * @property Category $category
 * @property Category[] $categories
 * @property Room[] $rooms
 * @property Attribute[] $prod_attributes
 *
 * @property Video[] $videos
 *
 * @property Promotion[] $promotions Все акции в которых есть товар
 * @property Equivalent $equivalent
 * @property EquivalentProduct $equivalent_product
 * @property Product[] $related
 * @property Product[] $bonus
 * @property Brand $brand
 * @property Group[] $groups
 * @property Modification $modification
 * @property Modification $main_modification
 * @property ModificationProduct $modification_product
 * @property Series $series
 * @property ProductPrice $prices
 *
 * @property CartStorage[] $cartStorages
 * @property CartCookie[] $cartCookies
 * @property Wish[] $wishes
 * @property OrderReserve[] $reserves
 * @property StorageItem[] $storageItems
 * @property OrderItem[] $orderItems
 * @property Review[] $reviews
 * @property Review[] $reviewsAll
 * @property ParserProduct $parser
 * @property Product[] $composites
 * @property BalanceProduct $balance - минимальный и макисмальный объемы для авто заказа
 */
class Product extends Model
{
    use SoftDeletes;//, GalleryField;
    protected $touches = ['categories', 'category', 'rooms'];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'dimensions' => DimensionsCast::class,
        'packages' => 'json',
        'published' => 'boolean',
        'not_sale' => 'boolean',
        'only_on_order' => 'boolean',
    ];
    protected $attributes = [
        'short' => '',
        'dimensions' => '{}',
        'packages' => '[]',
        'description' => '',
        'current_rating' => 0,
        'published' => false,
        'pre_order' => true,
        'series_id' => null,
        'published_at' => null,
        'priority' => false,
        'not_sale' => false,
        'fractional' => false,
        'only_offline' => false,
    ];
    protected $fillable = [
        'name',
        'slug',
        'code',
        'status',
        'description',
        'care',
        'short',
        'main_category_id',
        'brand_id',
        'current_rating',
        'published',
        'only_offline ',
        'pre_order',
        'delivery',
        'local',
        'code_search',
        'series_id',
        'not_sale',
        'priority',
        'packages',
        'fractional',
        'complexity',
        'only_on_order',

    ];
    protected $hidden = [

    ];
    protected $with = [
        'brand',
    ];

    public function gallery(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');
    }

    public function sluggable()
    {
        return ['slug' => ['source' => 'name']];
    }

    //РЕГИСТРАТОРЫ

    /**
     * Регистрация товара, с учетом дублей по имени - добавляем номер
     * Для дублирования артикула - вызываем исключение
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

    /**
     * Доступен для покупки
     */
    public function isSale(): bool
    {
        return $this->not_sale == false;
    }

    /**
     * Дробное кол-во товара
     */
    public function isFractional(): bool
    {
        return $this->fractional == true;
    }


    public function isPublished(): bool
    {
        return $this->published == true;
    }


    public function isBalance(): bool
    {
        return $this->getQuantity() < $this->balance->min;
    }

    public function isMarking(): bool
    {
        return $this->marking_type_id != null;
    }

    //*** GET-....

    public function weight(): float|int
    {
        return 0;
    }

    public function volume(): float|int
    {
        $volume = 0;
        if ($this->composites()->count() > 0) {
            foreach ($this->composites as $composite)
                $volume += $composite->volume() * $composite->pivot->quantity;
        } else {
            $volume = $this->packages->volume();
        }

        return ceil($volume * 10000) / 10000;
    }

    /**
     * Кол-во доступное для продажи по всем точкам, за минусом резерва
     */
    #[Pure]
    public function getQuantitySell(): float
    {
        //return $this->count_for_sell;
        return $this->getQuantity() - $this->getQuantityReserve();
    }

    /**
     * Кол-во товара на складах
     */
    public function getQuantity(?int $storage_id = null): float
    {
        $query = StorageItem::selectRaw('SUM(quantity * 1) AS total')->where('product_id', $this->id);

        if (!is_null($storage_id)) $query->where('storage_id', $storage_id);
        $quantity = $query->first();
        return (float)$quantity->total ?? 0;

    }

    #[Pure] public function getQuantityReserve(): float
    {
        $quantity = 0;
        foreach ($this->storageItems as $storageItem) {
            $quantity += $storageItem->getQuantityReserve();
        }
        return $quantity;
    }


    /**
     * Значение атрибута по его Id
     */
    public function Value(int $attribute_id)
    {

        foreach ($this->prod_attributes as $attribute) {
            if ($attribute->id === $attribute_id) {
                return $attribute->Value();
            }
        }
        return null;
    }

    public function getProdAttribute(int $id_attr): ?Attribute
    {
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
     * Действующая акция или null
     * @return Promotion|null
     */
    public function promotion(): ?Promotion
    {
        foreach ($this->promotions as $promotion) {
            if ($promotion->status == 'started') return $promotion;
        }
        return null;
    }

    //*** RELATIONS

    //ТОВАРНЫЙ УЧЕТ, ПРОДАЖИ

    public function storageItems(): HasMany
    {
        return $this->hasMany(StorageItem::class, 'product_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'promotions_products',
            'product_id', 'promotion_id')->withPivot('price');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(BalanceProduct::class)->withDefault(['min' => 1, 'buy' => true]);
    }

    //Клиентские связи - корзина, избранное
    public function cartStorages(): HasMany
    {
        return $this->hasMany(CartStorage::class, 'product_id', 'id');
    }

    public function cartCookies(): HasMany
    {
        return $this->hasMany(CartCookie::class, 'product_id', 'id');
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class, 'product_id', 'id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id')->where('status', Review::STATUS_PUBLISHED)->orderByDesc('created_at');
    }

    public function reviewsAll(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }


    //ХАРАКТЕРИСТИКИ ТОВАРА

    public function parser(): HasOne
    {
        return $this->hasOne(ParserProduct::class, 'product_id', 'id');
    }

    public function composites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'composites', 'parent_id', 'child_id')->withPivot('quantity');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'main_category_id', 'id');
    }

    public function related(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_id');
    }

    public function bonus(): BelongsToMany //Для attach и detach
    {
        return $this->belongsToMany(Product::class, 'bonus_products', 'product_id', 'bonus_id')->withPivot('discount');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categories_products', 'product_id', 'category_id');
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'rooms_products', 'product_id', 'room_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tags_products', 'product_id', 'tag_id');
    }

    public function prod_attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            Attribute::class, 'attributes_products',
            'product_id', 'attribute_id')->withPivot('value');
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }

    public function main_modification(): HasOne
    {
        return $this->hasOne(Modification::class, 'base_product_id', 'id');
    }

    public function modification_product(): HasOne
    {
        return $this->hasOne(ModificationProduct::class, 'product_id', 'id');
    }

    public function modification(): HasOneThrough
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

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'groups_products', 'product_id', 'group_id');
    }

    public function equivalent_product(): HasOne
    {
        return $this->hasOne(EquivalentProduct::class, 'product_id', 'id');
    }

    public function equivalent(): HasOneThrough
    {
        return $this->hasOneThrough(
            Equivalent::class,
            EquivalentProduct::class,
            'product_id', 'id', 'id',
            'equivalent_id');
    }

    //СПРАВОЧНИКИ GUIDE

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function measuring(): BelongsTo
    {
        return $this->belongsTo(Measuring::class, 'measuring_id', 'id')->withDefault(['name' => 'шт']);
    }

    public function markingType(): BelongsTo
    {
        return $this->belongsTo(MarkingType::class, 'marking_type_id', 'id');
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

    public function updateReview(): void
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



    /////////////////////

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'id');
    }


}
