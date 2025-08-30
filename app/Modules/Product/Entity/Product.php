<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Accounting\Entity\BalanceProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Base\Casts\DimensionsCast;
use App\Modules\Base\Casts\PackagesCast;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Packages;
use App\Modules\Base\Entity\Video;
use App\Modules\Base\Traits\GalleryField;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Guide\Entity\Country;
use App\Modules\Guide\Entity\MarkingType;
use App\Modules\Guide\Entity\Measuring;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Parser\Entity\ProductParser;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;
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
 * @property string $comment Комментарий, для специалистов. Клиентам не видно
 *
 * @property int $main_category_id
 * @property int $frequency частота покупки
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
 *
 * @property Dimensions $dimensions Габариты товара (+ вес),
 * @property Packages $packages Упаковки + вес + кол-во пачек
 * @property string $model Модель товара, не для всех товаров, опционо
 * @property string $barcode Штрих-код
 * @property bool $fractional  Дробное кол-во при учете
 * @property bool $hide_price Не указывать в прайс листах
 *
 * @property int $vat_id НДС
 * @property int $country_id Страна
 * @property int $measuring_id Ед.измерения
 * @property int $marking_type_id Вид продукции
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 *
 * @property VAT $VAT
 * @property Country $country
 * @property Measuring $measuring
 * @property MarkingType $markingType
 *
 * @property Tag[] $tags
 * @property Category $category
 * @property Category[] $categories
 * @property Attribute[] $prod_attributes
 *
 *
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
 * @property Modification $main_modification
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
 * @property Product[] $composites
 * @property BalanceProduct $balance - минимальный и макисмальный объемы для авто заказа
 */
class Product extends Model
{
    use SoftDeletes, GalleryField;

    const int FREQUENCY_MAJOR = 101;
    const int FREQUENCY_AVERAGE = 102;
    const int FREQUENCY_SMALL = 103;
    const int FREQUENCY_PERIOD = 104;
    const int FREQUENCY_NOT = 105;
    const array FREQUENCIES = [
        self::FREQUENCY_MAJOR => 'Крупная покупка (от 3 лет)',
        self::FREQUENCY_AVERAGE => 'Средняя покупка (1-3 года)',
        self::FREQUENCY_SMALL => 'Ходовой товар, с небольшим сроком пользования',
        self::FREQUENCY_PERIOD => 'Расходный товар',
        self::FREQUENCY_NOT => 'Нет',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'published_at' => 'datetime',
        'dimensions' => DimensionsCast::class,
        'packages' => PackagesCast::class,
    ];
    protected $attributes = [
        'short' => '',
        'dimensions' => '{}',
        'packages' => '[]',
        'description' => '',
        'frequency' => self::FREQUENCY_NOT,
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
        'short',
        'main_category_id',
        'brand_id',
        'frequency',
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
    ];
    protected $hidden = [

    ];
    protected $with = [
        'brand',
    ];

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

    public function isNew(): bool
    {
        if ($this->published_at == null) return false;
        if ($this->published_at->gte(now()->subMonths(2))) return true;
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
        return $this->local == true;
    }

    public function isDelivery(): bool
    {
        return $this->delivery == true;
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

    public function isWish(int $user_id): bool
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

    public function isComposite(int $product_id): bool
    {
        foreach ($this->composites as $composite) {
            if ($composite->id == $product_id) return true;
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

    public function isBalance(): bool
    {
        return $this->getQuantity() < $this->balance->min;
    }


    public function isMarking(): bool
    {
        return $this->marking_type_id != null;
    }

    //*** SET-....
    //SET и GET
    /**
     * Снять с продажи (показываться на сайте будет, купить нельзя)
     */
    public function setNotSale(): void
    {
        $this->not_sale = true;
        $this->save();
    }

    /**
     * Вернуть в продажу
     */
    public function setForSale(): void
    {
        $this->not_sale = false;
        $this->save();
    }

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

    public function setVAT(int $vat_id): void
    {
        $this->vat_id = $vat_id;
        $this->save();
    }

    //*** GET-....

    public function weight(): float|int
    {
        $weight = 0;
        if ($this->composites()->count() > 0) {
            foreach ($this->composites as $composite)
            $weight += $composite->weight() * $composite->pivot->quantity;
        } else {
            $weight = $this->packages->weight();
        }
        return ceil($weight * 1000) /1000;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Если есть модификация возвращает ее название, иначе название товара
     */
    public function getName(): string
    {
        if (is_null($this->modification)) return $this->name;
        return $this->modification->name;
    }

    //*** ЦЕНЫ

    /**
     * Текущая (Предыдущая $previous = true) цена для клиента (с учетом цен клиента, Розничная, или Оптовые)
     * Показывать на сайте (Фронтенд)
     */
    //TODO Переделать, везде запрашивать $user
    public function getPrice(bool $previous = false, User $user = null): float
    {
        $price = 0;
        if (!$this->isSale()) return $price;
        if (is_null($user)) {
            $user = Auth::guard('user')->user();
        }

        if (!is_null($user)) {
            if ($user->isBulk() && $this->getPriceBulk($previous) != 0) $price = $this->getPriceBulk($previous); //Оптовый клиент
            if ($user->isSpecial() && $this->getPriceSpecial($previous) != 0) $price = $this->getPriceSpecial($previous); //Спец Клиент
        }
        //Проверяем установленные цены
        if ($price == 0) $price = $this->getPriceRetail($previous);
        //Проверяем парсер
        //dd(1);
        if ($price == 0) $price = $this->getPriceParser($previous);
        //Проверяем модификацию
        if ($price == 0 && !is_null($this->modification) && ($this->id != $this->modification->base_product_id)) {
            return $this->modification->base_product->getPrice($previous, $user);
        }

        return $price;
    }

    //ЦЕНЫ ДЛЯ АДМИНКИ
    /**
     * Последняя закупочная цена, через Поставщиков
     */
    public function getPriceCost(bool $previous = false): float
    {
        if ($this->pricesCost()->count() == 0) return 0;
        if ($previous) {
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
        if ($previous) {
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
        if ($previous) {
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
        if ($previous) {
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
        //Если минимальная не установлена, возвращается себестоимость
        if ($this->pricesMin()->count() == 0) return 0;
        if ($previous) {
            /** @var ProductPriceMin $model */
            $model = $this->pricesMin()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesMin()->skip(0)->first();
        }
        return $model->value;
    }

    public function getPriceParser(bool $previous = false): float
    {
        if (is_null($this->parser)) return 0;
      //TODO берем базовую цену  $price = $previous ? $this->parser->price_base : $this->parser->price_sell;
      //  Log::info(json_encode([$this->code, $this->parser->price_base, $this->brand->currency->fixed]));
        $price = $this->parser->price_base;
        return ceil($price * ($this->brand->currency->fixed));
    }

    /**
     * Цена на предзаказ, если цена не определена, возвращаем из парсера или текущую
     */
    public function getPricePre(bool $previous = false): float
    {
        if ($this->pricesPre()->count() == 0) return $this->getPriceParser($previous);
        if ($previous) {
            $model = $this->pricesPre()->skip(1)->first();
            if (empty($model)) return 0;
        } else {
            $model = $this->pricesPre()->skip(0)->first();
        }
        return $model->value;
    }

    //*** КОЛ_ВО
    public function getReserveCount(): float
    {
        $result = 0;
        foreach ($this->storageItems as $storageItem) {
            $result += $storageItem->getQuantityReserve();
        }
        return $result;
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
    public function getQuantity(int $storage_id = null): float
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
     * Хранилища, где данный товар есть в наличии
     * @return Storage[]
     */
    public function getStorages(): array
    {
        return Storage::whereHas('items', function ($q) {
            $q->where('product_id', $this->id);
        })->getModels();
    }

    /**
     * @return StorageItem[]
     */
    public function getStorageItems(): array
    {
        return StorageItem::where('product_id', $this->id)->getModels();
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

    //ЦЕНЫ ****************
    public function pricesCost(): HasMany
    {
        return $this->hasMany(ProductPriceCost::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesRetail(): HasMany
    {
        return $this->hasMany(ProductPriceRetail::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesBulk(): HasMany
    {
        return $this->hasMany(ProductPriceBulk::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesSpecial(): HasMany
    {
        return $this->hasMany(ProductPriceSpecial::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesMin(): HasMany
    {
        return $this->hasMany(ProductPriceMin::class, 'product_id', 'id')->orderByDesc('id');
    }

    public function pricesPre(): HasMany
    {
        return $this->hasMany(ProductPricePre::class, 'product_id', 'id')->orderByDesc('id');
    }

    //ХАРАКТЕРИСТИКИ ТОВАРА

    public function parser(): HasOne
    {
        return $this->hasOne(ProductParser::class, 'product_id', 'id');
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
    public function VAT(): BelongsTo
    {
        return $this->belongsTo(VAT::class, 'vat_id', 'id')->withDefault(['name' => 'Без НДС', 'value' => null]);
    }

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

    /**
     * Массив данных для поиска ajax
     */
    public function toArrayForSearch(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'code_search' => $this->code_search,
            'image' => $this->getImage(),
            'price' => $this->getPrice(),
            'url' => route('admin.product.edit', $this),
            'count' => $this->getQuantitySell(),
            'stock' => $this->getQuantitySell() > 0,
        ];
    }

}
