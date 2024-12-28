<?php
declare(strict_types=1);

namespace App\Modules\Guide\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property boolean $honest
 * @property Product[] $products
 */
class MarkingType extends Model
{

    protected $table = 'guide_marking_type';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'honest',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
            'honest' => true,
        ]);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'marking_type_id', 'id');
    }
}
