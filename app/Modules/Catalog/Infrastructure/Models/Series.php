<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $name_ru
 * @property Product[] $products
 */
class Series extends Model
{

    public $timestamps = false;
    protected $table = 'series';
    protected $fillable = [
        'name',
        'name_ru',
        ];

    public static function register(string $name)
    {
        return self::create([
            'name' => $name,
        ]);
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'series_id', 'id');
    }
}
