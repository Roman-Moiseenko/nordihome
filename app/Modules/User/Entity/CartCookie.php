<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property string $user_ui
 * @property int $product_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $options_json //Опции товара [id1, id2, ...]
 *
 * @property Product $product
 */
class CartCookie extends Model
{
    protected $table = 'cart_cookie';

    protected $fillable = [
        'user_ui',
        'product_id',
        'quantity',
        'options_json',
    ];
    public static function register(string $user_ui, int $product_id, int $quantity, array $options_json = []): self
    {
        return self::create([
            'user_ui' => $user_ui,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'options_json' => json_encode($options_json)
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
