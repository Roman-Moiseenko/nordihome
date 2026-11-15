<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $name_en
 * @property string $slug
 * @property string|null $value
 * @property array|null $variants
 * @property bool $is_html
 */
class AvitoParameter extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'value',
        'variants',
        'is_html',
    ];

    protected $casts = [
        'variants' => 'array',
        'is_html' => 'boolean',
    ];
}
