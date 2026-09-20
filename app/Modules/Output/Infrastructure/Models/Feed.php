<?php

namespace App\Modules\Output\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $active
 *
 * @property array $products_in
 * @property array $products_out
 *
 * @property array $categories_in
 * @property array $categories_out
 *
 * @property array $rooms_in
 * @property array $rooms_out
 *
 * @property array $promotions_in
 * @property array $promotions_out
 *
 * @property array $groups_in
 * @property array $groups_out
 *
 * @property array $tags_in
 * @property array $tags_out
 *
 * @property bool $set_preprice
 * @property string $set_title
 * @property string $set_description
 *
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 */
class Feed extends Model
{
    protected $attributes = [
        'products_in' => '[]',
        'products_out' => '[]',
        'categories_in' => '[]',
        'categories_out' => '[]',
        'rooms_in' => '[]',
        'rooms_out' => '[]',
        'promotions_in' => '[]',
        'promotions_out' => '[]',
        'groups_in' => '[]',
        'groups_out' => '[]',
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
        'rooms_in' => 'array',
        'rooms_out' => 'array',
        'promotions_in' => 'array',
        'promotions_out' => 'array',
        'groups_in' => 'array',
        'groups_out' => 'array',
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
