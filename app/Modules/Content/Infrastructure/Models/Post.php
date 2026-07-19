<?php
declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Entity\Renders\RenderPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $caption
 * @property string $fragment
 * @property array $meta
 * @property string $template
 * @property bool $published
 * @property string|null $published_at
 * @property PostCategory $category
 * @property bool $old_render
 * @property string $text
 */
class Post extends RenderPage
{
    use ImageField;

    protected $fillable = [
        'name',
        'slug',
        'template',
        'caption',
        'fragment',
        'meta',
        'published',
        'published_at',
        'category_id',
        'old_render',
        'text',
    ];

    protected $casts = [
        'meta' => 'array',
        'published' => 'boolean',
        'published_at' => 'datetime',
        'old_render' => 'boolean',
    ];

    public $timestamps = true;
    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }
}
