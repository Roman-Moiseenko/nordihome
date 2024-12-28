<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $value
 * @property Product[] $products
 */
class VAT extends Model
{
    protected $table = 'guide_v_a_t';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'value',
    ];
    public static function register(string $name, int $value)
    {
        return self::create([
            'name' => $name,
            'value' => $value,
        ]);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vat_id', 'id');
    }
}
