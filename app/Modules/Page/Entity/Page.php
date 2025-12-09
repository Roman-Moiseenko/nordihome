<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Page\Entity\Renders\RenderPage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $template
 * @property int $sort
 * @property Page $parent
 */
class Page extends RenderPage
{
    use ImageField, IconField;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'title',
        'description',
        'template',
        'sort',
        'published',
        'text'
    ];

    public static function register(string $name, string $slug,
                                    string $title, string $description, string $template, int $parent_id = null): self
    {
        $sort = Page::where('parent_id', $parent_id)->count();
        return self::create([
            'parent_id' => $parent_id,
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'title' => $title,
            'description' => $description,
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
