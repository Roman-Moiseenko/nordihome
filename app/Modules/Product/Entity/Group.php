<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Page\Entity\DataWidget;
use App\Modules\Page\Entity\DataWidgetInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $published
 * @property string $description
 * @property Product[] $products
// * @property Promotion[] $promotions
 */
class Group extends Model implements DataWidgetInterface
{
    use ImageField;

    public $timestamps = false;

    protected $attributes = [
        'published' => false,
    ];

    protected $fillable = [
        'name', 'description', 'slug', 'published'
    ];

    public static function register(string $name, string $description = '', string $slug = '', bool $published = false): static
    {
        return static::create([
            'name' => $name,
            'description' => $description,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'published' => $published,
        ]);
    }


    public function setVisible(array $visible)
    {
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'groups_products', 'group_id', 'product_id');
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
        if (!empty($this->slug)) $data->url = route('shop.group.view', $this->slug);
        $data->image = $this->getImage();
        $data->title = $this->name;
        $data->items = array_map(function (Product $product) {
            return [
                'image' => $product->getImage(),
                'url' => route('shop.product.view', $product->slug),
                'title' => $product->getName(),
                'price' => $product->getPrice(),
                'count' => $product->getCountSell(),
            ];
        }, $this->products()->getModels());
        return $data;
    }
}
