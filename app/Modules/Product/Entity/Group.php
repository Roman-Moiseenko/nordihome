<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property Photo $photo
 * @property Product[] $products
 */
class Group extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'description',
    ];

    public static function register(string $name, string $description = ''): static
    {
        return static::create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'groups_products', 'group_id', 'product_id');
    }
}
