<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property float $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $options_json //Опции товара [id1, id2, ...]
 * @property bool $check
 * @property Product $product
 * @property bool $is_parser
 */

class CartStorage extends Model
{
    protected $attributes = [
        'check' => true,
        'options_json' => '{}',
        ];
    protected $table = 'cart_storage';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'options_json',
        'check',
        'is_parser',
    ];

    protected $casts = [
        'check' => 'boolean',
        'is_parser' => 'boolean',
        'options_json' => 'json',
    ];

    public static function register(int $user_id, int $product_id, float $quantity, bool $is_parser): self
    {
        return self::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'is_parser' => $is_parser,
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
