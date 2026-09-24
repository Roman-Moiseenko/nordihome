<?php
declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Models;

use App\Modules\Content\Entity\Renders\RenderPage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $svg
 * @property string $slug
 * @property string $template
 * @property int $sort
 * @property Page $parent
 */
class Page extends RenderPage
{


    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'template',
        'sort',
        'published',
        'text'
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
