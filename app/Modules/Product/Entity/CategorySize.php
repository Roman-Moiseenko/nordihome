<?php

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Size[] $sizes
 */
class CategorySize extends Model
{
    public $timestamps = false;
    protected $table = 'category_size';

    protected $fillable = [
        'name',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
        ]);
    }
    public function sizes()
    {
        return $this->hasMany(Size::class, 'category_size_id');
    }

}
