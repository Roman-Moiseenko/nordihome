<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Pages\Entity\DataWidget;
use App\Modules\Pages\Entity\DataWidgetInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Photo $photo
 * @property Product[] $products
 * @property Promotion[] $promotions
 */
class Group extends Model implements DataWidgetInterface
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'description',
    ];

    public static function register(string $name, string $description = ''): static
    {
        return static::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'groups_products', 'group_id', 'product_id');
    }


    public function getImage()
    {
        if (empty($this->photo->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->photo->getUploadUrl();
        }
    }

    public function isProduct(int $id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $id) return true;
        }
        return false;
    }

    public function getDataWidget(array $params = []): DataWidget
    {
        $data = new DataWidget();
        $data->image = $this->photo;
        $data->title = $this->name;
        $data->items = array_map(function (Product $product) {
            return [
                'image' => $product->photo,
                'url' => route('shop.product.view', $product),
                'title' => $product->getName(),
                'price' => $product->lastPrice->value,
                'count' => $product->count_for_sell,
            ];
        }, $this->products()->getModels());
        return $data;
    }
}
