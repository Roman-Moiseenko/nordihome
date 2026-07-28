<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $user_ui
 * @property int $product_id
 * @property float $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $options_json //Опции товара [id1, id2, ...]
 * @property bool $is_parser
 * @property bool $check
 * @property Product $product
 */
class CartCookie extends Model
{
    protected $table = 'cart_cookie';
    protected $casts = [
        'check' => 'boolean',
        'is_parser' => 'boolean'
        ];
    protected $attributes = [
        'check' => true,
        'options_json' => '{}',
        ];
    protected $fillable = [
        'user_ui',
        'product_id',
        'quantity',
        'options_json',
        'check',
        'is_parser',
    ];
    public static function register(string $user_ui, int $product_id, float $quantity, bool $is_parser): self
    {
        return self::create([
            'user_ui' => $user_ui,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'is_parser' => $is_parser
        ]);
    }

    public function check(): void
    {
        $this->check = !$this->check;
        $this->save();
    }

    public function isCheck(): bool
    {
        return $this->check == true;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
