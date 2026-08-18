<?php
declare(strict_types=1);

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
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

    public $fillable = [
        'modelable_id',
        'modelable_type',
        'button_name'
    ];
    public function modelable()
    {
        return $this->morphTo();
    }

    public function getUrl(): string
    {
        if (!empty($this->url)) return $this->url;
        if ($this->modelable instanceof Category::class) {
            return route('shop.category.view', $this->modelable->slug);
        }
        if ($this->modelable instanceof Group::class) {
            return route('shop.group.view', $this->modelable->slug);
        }
        return '';
    }

    /**
     * @return Product[]
     */
    public function products(): array
    {
        return $this->modelable->products;
    }

}
