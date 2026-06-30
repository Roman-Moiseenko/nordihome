<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $svg
 * @property array $meta
 * @property bool $published
 * @property int $_lft
 * @property int $_rgt
 * @property int $depth
 * @property int|null $parent_id
 */
class Room extends Model
{
    use NodeTrait, ImageField, IconField;

    protected $table = 'rooms';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'svg',
        'meta',
        'published',
        'parent_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'published' => 'boolean',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
}
