<?php

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $category_size_id
 * @property string $name
 */
class Size extends Model
{
    public $timestamps = false;

    protected $fillable =[
        'name',
        'category_size_id',
    ];

    public static function new(string $name): self
    {
        return self::make(['name' => $name]);
    }
}
