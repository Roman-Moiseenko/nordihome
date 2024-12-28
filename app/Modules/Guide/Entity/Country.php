<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property Product[] $products
 */
class Country extends Model
{
    protected $table = 'guide_country';
    public $timestamps = false;
    protected $fillable = [
        'name',
    ];

    public static function register(string $name)
    {
        return self::create([
            'name' => $name,
        ]);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'country_id', 'id');
    }
}
