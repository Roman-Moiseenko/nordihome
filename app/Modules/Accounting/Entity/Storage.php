<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property float $latitude
 * @property float $longitude
 * @property string $post //Почтовый индекс
 * @property string $city
 * @property string $address // заменить
 * @property bool $point_of_sale
 * @property bool $point_of_delivery
 * @property bool $default
 * @property StorageItem[] $items
 * @property StorageDepartureItem[] $departureItems
 * @property StorageArrivalItem[] $arrivalItems
 */
class Storage extends Model
{
    use ImageField;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
        'post',
        'city',
        'address',
        'point_of_sale',
        'point_of_delivery',
        'default',
    ];

    public static function register(string $name, bool $point_of_sale = true, bool $point_of_delivery = true, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'point_of_sale' => $point_of_sale,
            'point_of_delivery' => $point_of_delivery,
        ]);
    }

    //*** SET-...
    public function setAddress(string $post, string $city, string $address): void
    {
        $this->update([
            'post' => $post,
            'city' => $city,
            'address' => $address,
        ]);

    }

    public function setCoordinate(float $latitude, float $longitude): void
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function setDefault(): void
    {
        $this->default = true;
        $this->save();
    }

    //*** GET-...

    /**
     * Кол-во единиц товара на складе, если не указан id, то всего товара
     */
    public function getQuantity(int $product_id = null): float
    {
        //Более быстрый вариант
        if (is_null($product_id)) {
            $quantity = StorageItem::selectRaw('SUM(quantity) AS total')->where('storage_id', $this->id)->first();
            return (float)$quantity->total ?? 0;
        } else {
            return StorageItem::where('storage_id', $this->id)->where('product_id', $product_id)->pluck('quantity')->sum();
        }
/*
        $storageItem = $this->getItem($product_id);
        if (is_null($storageItem)) return 0;
        return $storageItem->quantity;*/
    }

    #[Pure]
    public function getReserve(Product $product): float
    {
        $storageItem = $this->getItem($product->id);
        if (is_null($storageItem)) return 0;

        return $storageItem->getQuantityReserve();
    }

    public function getAvailable(Product $product): float
    {
        return $this->getQuantity($product->id) - $this->getReserve($product) - $this->getDeparture($product);
    }

    public function getItem(int $product_id):? StorageItem
    {
        foreach ($this->items as $item) {
            if ($item->product_id == $product_id) {
                return $item;
            }
        }
        return null;
    }

    public function getDeparture(Product $product): float
    {
        //Более быстрый вариант
        return StorageDepartureItem::where('storage_id', $this->id)
            ->where('product_id', $product->id)
            ->pluck('quantity')->sum();
        /*
        $result = 0;
        foreach ($this->departureItems as $departureItem) {
            if ($departureItem->product_id == $product->id) $result += $departureItem->quantity;
        }
        return $result;
        */
    }

    public function getArrival(Product $product): float
    {
        //Более быстрый вариант
        return StorageArrivalItem::where('storage_id', $this->id)->where('product_id', $product->id)->pluck('quantity')->sum();

        /*
        $result = 0;
        foreach ($this->arrivalItems as $arrivalItem) {
            if ($arrivalItem->product_id == $product->id) $result += $arrivalItem->quantity;
        }
        return $result;
        */
    }

    //*** RELATIONS
    public function items(): HasMany
    {
        return $this->hasMany(StorageItem::class, 'storage_id', 'id')->whereHas('product', function ($query) {
            $query->where('deleted_at', null);
        });
    }

    public function departureItems(): HasMany
    {
        return $this->hasMany(StorageDepartureItem::class, 'storage_id', 'id');
    }

    public function arrivalItems(): HasMany
    {
        return $this->hasMany(StorageArrivalItem::class, 'storage_id', 'id');
    }

    public function add(Product $product, float $quantity): StorageItem
    {
        foreach ($this->items as $item) {
            if ($item->product_id == $product->id) {
                $item->quantity += $quantity;
                $item->save();
                return $item;
            }
        }
        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity
        ]);
    }

    public function sub(Product $product, float $quantity): void
    {
        foreach ($this->items as $item) {
            if ($item->product_id == $product->id) {
                $item->quantity -= $quantity;
                $item->save();
                return;
            }
        }
    }



}
