<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Entity\Photo;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $organization_id
 * @property float $latitude
 * @property float $longitude
 * @property string $post //Почтовый индекс
 * @property string $city
 * @property string $address // заменить
 * @property bool $point_of_sale
 * @property bool $point_of_delivery
 * @property Photo $photo
 * @property Organization $organization
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
        'organization_id',
        'latitude',
        'longitude',
        'post',
        'city',
        'address',
        'point_of_sale',
        'point_of_delivery'
    ];

    public static function register(int $organization_id, string $name, bool $point_of_sale, bool $point_of_delivery, string $slug = ''): self
    {
        return self::create([
            'organization_id' => $organization_id,
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'point_of_sale' => $point_of_sale,
            'point_of_delivery' => $point_of_delivery,
        ]);
    }

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

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

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

    public function add(Product $product, int $quantity)
    {
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
                $item->quantity += $quantity;
                $item->save();
                return;
            }
        }
        $this->items()->create([
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

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function getImage(): string
    {
        if (empty($this->photo->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->photo->getUploadUrl();
        }
    }

    public function getQuantity(Product $product): int
    {
        //Более быстрый вариант
        return StorageItem::where('storage_id', $this->id)->where('product_id', $product->id)->pluck('quantity')->sum();
/*
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
                return $item->quantity;
            }
        }
        return 0;*/
    }

    public function getReserve(Product $product): int
    {
        return Reserve::where('storage_id', $this->id)->where('product_id', $product->id)->pluck('quantity')->sum();
    }

    //TODO Использование ??? Переименовать в getAvailable ???
    public function getAvailable(Product $product): int
    {
        return $this->getQuantity($product) - $this->getReserve($product) - $this->getDeparture($product);
    }

    public function getItem(Product $product): StorageItem
    {
        foreach ($this->items as $item) {
            if ($item->product->id == $product->id) {
                return $item;
            }
        }
        throw new \DomainException('Товар Id=' . $product->id . ' В хранилище не найден');
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

   /* public function getAvailable(Product $product): int
    {

    }*/
}
