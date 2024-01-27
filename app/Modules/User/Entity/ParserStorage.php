<?php


namespace App\Modules\User\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParserStorage
 * @package App\Modules\Shop\Parser\
 * @property int $id
 * @property int $user_id
 * @property string $user_ui
 * @property int $product_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Product $product
 */
class ParserStorage extends Model
{
    protected $fillable = [
        'user_id',
        'user_ui',
        'product_id',
        'quantity',
    ];
    protected $table = 'parser_storage';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function registerForGuest(string $user_ui, int $product_id, int $quantity): self
    {
        return self::create([
           'user_id' => null,
            'user_ui' => $user_ui,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
    }

    public static function registerForUser(int $user_id, int $product_id, int $quantity): self
    {
        return self::create([
            'user_id' => $user_id,
            'user_ui' => null,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
