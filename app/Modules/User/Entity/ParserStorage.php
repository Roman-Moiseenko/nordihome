<?php


namespace App\Modules\Shop\Parser;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParserStorage
 * @package App\Modules\Shop\Parser\
 * @property int $id
 * @property int $user_id
 * @property string $user_uuid
 * @property int $product_id
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ParserStorage extends Model
{
    protected $fillable = [
        'user_id',
        'user_uuid',
        'product_id',
        'quantity',
    ];
    protected $table = 'parser_storage';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function registerForGuest(string $user_uuid, int $product_id, int $quantity): self
    {
        return self::create([
           'user_id' => null,
            'user_uuid' => $user_uuid,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
    }

    public static function registerForUser(int $user_id, int $product_id, int $quantity): self
    {
        return self::create([
            'user_id' => $user_id,
            'user_uuid' => null,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
    }


}
