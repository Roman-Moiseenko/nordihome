<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $published
 * @property string $description
 * @property array $meta
 * @property Product[] $products
 */
class Group extends Model
{
    use ImageField;

    public $timestamps = false;

    protected $attributes = [
        'published' => false,
        'meta' => '[]',
    ];

    protected $fillable = [
        'name', 'description', 'slug', 'published'
    ];

    protected $casts = [
        'meta' => 'array',
    ];


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'groups_products', 'group_id', 'product_id');
    }


    public function scopeActive($query)
    {
        return $query->where('published', true);
    }

}
