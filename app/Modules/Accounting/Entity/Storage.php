<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
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
 * @property Photo $photo
 * @property StorageItem[] $items
 * @property StorageDepartureItem[] $departureItems
 * @property StorageArrivalItem[] $arrivalItems
 */
class Storage extends Model
{
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

    public static function register(string $name, bool $point_of_sale, bool $point_of_delivery, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'point_of_sale' => $point_of_sale,
            'point_of_delivery' => $point_of_delivery,
        ]);
    }

    //*** SET-...
    public function setAddress(string $post, string $city, string $address)
    {
        $this->update([
            'post' => $post,
            'city' => $city,
            'address' => $address,
        ]);

    }

    public function setCoordinate(float $latitude, float $longitude)
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

    public function getImage(): string
    {
        if (empty($this->photo->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->photo->getUploadUrl();
        }
    }

    #[Pure]
    public function getQuantity(Product $product): int
    {
        //Более быстрый вариант
        //return StorageItem::where('storage_id', $this->id)->where('product_id', $product->id)->pluck('quantity')->sum();
        $storageItem = $this->getItem($product);
        if (is_null($storageItem)) return 0;
        return $storageItem->quantity;
    }

    #[Pure]
    public function getReserve(Product $product): int
    {
        $storageItem = $this->getItem($product);
        if (is_null($storageItem)) return 0;

        return $storageItem->getQuantityReserve();
    }

    public function getAvailable(Product $product): int
    {
        return $this->getQuantity($product) - $this->getReserve($product) - $this->getDeparture($product);
    }

    public function getItem(Product $product):? StorageItem
    {
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
                return $item;
            }
        }
        return null;
    }

    public function getDeparture(Product $product): int
    {
        //Более быстрый вариант
        return StorageDepartureItem::where('storage_id', $this->id)->where('product_id', $product->id)->pluck('quantity')->sum();
        /*
        $result = 0;
        foreach ($this->departureItems as $departureItem) {
            if ($departureItem->product_id == $product->id) $result += $departureItem->quantity;
        }
        return $result;
        */
    }

    public function getArrival(Product $product): int
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
    public function items()
    {
        return $this->hasMany(StorageItem::class, 'storage_id', 'id');
    }

    public function departureItems()
    {
        return $this->hasMany(StorageDepartureItem::class, 'storage_id', 'id');
    }

    public function arrivalItems()
    {
        return $this->hasMany(StorageArrivalItem::class, 'storage_id', 'id');
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function add(Product $product, int $quantity): StorageItem
    {
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
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

    public function sub(Product $product, int $quantity)
    {
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
                $item->quantity -= $quantity;
                $item->save();
                return;
            }
        }
    }



}
