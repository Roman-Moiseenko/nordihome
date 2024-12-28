<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property boolean $fractional
 * @property Product[] $products
 */
class Measuring extends Model
{
    protected $table = 'guide_measuring';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'fractional',
    ];

    public static function register(string $name, bool $fractional)
    {
        return self::create([
            'name' => $name,
            'fractional' => $fractional,
        ]);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'measuring_id', 'id');
    }
}
