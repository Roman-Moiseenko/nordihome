<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Infrastructure\Models;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $client_id
 * @property int $product_id
 * @property Carbon $created_at
 * @property Product $product
 * @property Client $client
 */
class Wish extends Model
{
    protected $table = 'wishes';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'product_id',
        'created_at',
    ];
    public static function register(int $client_id, int $product_id): self
    {
        return self::create([
            'client_id' => $client_id,
            'product_id' => $product_id,
            'created_at' => now(),
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
