<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $code
 * @property string $fractional_name
 * @property boolean $fractional
 * @property Product[] $products
 */
class Measuring extends Model
{
    protected $table = 'guide_measuring';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'code',
        'fractional',
        'fractional_name',
    ];

    public static function register(string $name, int $code, bool $fractional, string $fractional_name = '')
    {
        return self::create([
            'name' => $name,
            'code' => $code,
            'fractional' => $fractional,
            'fractional_name' => $fractional_name,
        ]);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'measuring_id', 'id');
    }
}
