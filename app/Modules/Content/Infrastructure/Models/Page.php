<?php
declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Content\Entity\Renders\RenderPage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string $svg
 * @property string $text
 * @property string $template
 * @property int $sort
 * @property bool $published
 * @property array $meta
 * @property Page $parent
 * @property bool $old_render
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 */
class Page extends RenderPage
{

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'template',
        'sort',
        'meta',
        'published',
        'text',
        'old_render'
    ];

    protected $casts = [
        'old_render' => 'bool',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'meta' => MetaCast::class,
    ];


    public static function register(string $name, string $slug,
                                    string $template, int $parent_id = null): self
    {
        $sort = Page::where('parent_id', $parent_id)->count();
        return self::create([
            'parent_id' => $parent_id,
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'template' => $template,
            'sort' => $sort,
            'published' => false,
            'text' => '',
        ]);
    }

    public function setText(string $text): void
    {
        $this->text = $text;
        $this->save();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id', 'id');
    }

}
