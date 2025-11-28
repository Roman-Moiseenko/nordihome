<?php

namespace App\Modules\Unload\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property array $products_in
 * @property array $products_out
 * @property array $categories_in
 * @property array $categories_out
 * @property array $tags_in
 * @property array $tags_out
 * @property bool $set_preprice
 * @property string $set_title
 * @property string $set_description
 *
 */
class Feed extends Model
{
    public $timestamps = false;

    protected $attributes = [
        'products_in' => '[]',
        'products_out' => '[]',
        'categories_in' => '[]',
        'categories_out' => '[]',
        'tags_in' => '[]',
        'tags_out' => '[]',
    ];
    protected $fillable = [
        'name',
        'active',
    ];
    protected $casts = [
        'products_in' => 'array',
        'products_out' => 'array',
        'categories_in' => 'array',
        'categories_out' => 'array',
        'tags_in' => 'array',
        'tags_out' => 'array',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
            'active' => false,
        ]);
    }
}
