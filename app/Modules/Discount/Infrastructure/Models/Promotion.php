<?php
declare(strict_types=1);

namespace App\Modules\Discount\Infrastructure\Models;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name //Имя для внутреннего использования
 * @property string $condition_url //Ссылка на страницу с условиями и правилами
 * @property Carbon $start_at
 * @property Carbon $finish_at
 * @property bool $menu
 * @property bool $show_title //Показывать заголовок акции на карточках
 * @property array $meta
 * @property string $slug  //По title, если существует, добавляем год
 * @property string $template
 * @property string $status
 * @property int $discount
 * @property string $color_class
 * @property string $position_class
 * @property string $text_tag
 * @property bool $show_tag
 * @property string $svg
 * @property bool $show_discount
 * @property Product[] $products
 */
class Promotion extends Model
{
    use ImageField;

    protected $attributes = [
        'meta' => '[]',
    ];
    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
        'show_tag' => 'boolean',
        'show_discount' => 'boolean',
        'meta' => 'array',
    ];
    public $timestamps = false;
    protected $fillable = [
        'name',
        'slug',
        'finish_at',
        'start_at',
        'show_title',
        'menu',
        'condition_url',
        'discount',
        'svg',
        'status',
        'meta',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => PromotionStatus::DRAFT,
        ]);
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $product_id) return true;
        }
        return false;
    }


    public function products(): BelongsToMany//: array
    {
        return $this->belongsToMany(
            Product::class, 'promotions_products',
            'promotion_id', 'product_id')->withPivot(['price']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'started');
    }
}
