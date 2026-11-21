<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $channel
 * @property string $svg
 * @property string $icon
 * @property string $color
 * @property string $url
 * @property int $sort
 * @property bool $published
 */
class Contact extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'url',
        'sort',
        'published',
        'slug',
        'svg',
        'channel',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];
}
