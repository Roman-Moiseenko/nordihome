<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Accounting\Application\Actions\ProductPrice\GetLatestPricesQuery;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Catalog\Infrastructure\Models\Series;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Shop\Application\DTOs\ClientContext;

/**
 * @property string $url
 * @property int $modelable_id
 * @property string $modelable_type
 * @property string $button_name
 * @property string $model_type,
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
        'url',
        'model_type',
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
        if ($this->modelable instanceof Promotion) {
            return route('shop.promotion.view', $this->modelable->slug);
        }
        if ($this->modelable instanceof Series) {
            return route('shop.series.view', $this->modelable->slug);
        }

        return '';
    }


    /**
     * @return DTOProductWidget[]
     */
    public function products(?int $quantity = null): array
    {
        $products = $this->modelable?->products?->all() ?? [];

        if ($quantity !== null)
            $products = array_slice($products, 0, $quantity);

        return array_map(fn(Product $product) => $this->modelToDTO($product), $products);
    }

    private function modelToDTO(Product $product): DTOProductWidget
    {
        $useCase = app()->make(GetLatestPricesQuery::class);
        $client = request()->attributes->get('client_context') ?? new ClientContext();

        $prices = $useCase->execute($product->id);
        return new DTOProductWidget(
            id: $product->id,
            name: $product->name,
            slug: $product->slug,
            price: $prices[$client->priceType]
        );
    }

}
