<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $url
 * @property int $modelable_id
 * @property string $modelable_type
 * @property string $button_name
 */
class ProductWidget extends Widget
{

    protected $table = "widget_products";
    const array MODELS = [
        'categories' => Category::class,
        'rooms' => Room::class,
        'groups' => Group::class,
    ];

    public $fillable = [
        'modelable_id',
        'modelable_type',
        'button_name',
        'url'
    ];
    public function modelable()
    {
        return $this->morphTo();
    }

    public function getUrl(): string
    {
        if (!empty($this->url)) return $this->url;
        if ($this->modelable instanceof Category) {
            return route('shop.category.view', $this->modelable->slug);
        }
        if ($this->modelable instanceof Group) {
            return route('shop.group.view', $this->modelable->slug);
        }
        if ($this->modelable instanceof Room) {
            return route('shop.room.view', $this->modelable->slug);
        }
        return '';
    }


    /**
     * @return Product[]
     */
    public function products(?int $quantity = null): array
    {
        $products = $this->modelable?->products?->all() ?? [];

        if ($quantity === null) {
            return $products;
        }

        return array_slice($products, 0, $quantity);
    }

}
