<?php
declare(strict_types=1);

namespace App\Modules\Pages\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $template
 * @property bool $menu
 * @property bool $published
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Page extends Model
{
    const PATH_TEMPLATES = 'admin.page.widget.template.';

    const PAGES_TEMPLATES = [
        'contact',
        'review',
        'tariff',
    ];
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'title',
        'description',
        'template',
        'menu',
        'sort',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $name, string $slug,
                                    string $title, string $description, string $template, bool $menu, int $parent_id = null): self
    {
        $sort = Page::where('parent_id', $parent_id)->max('sort');
        return self::create([
            'parent_id' => $parent_id,
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'title' => $title,
            'description' => $description,
            'template' => $template,
            'sort' => ($sort + 1),
            'menu' => $menu,
            'published' => false,
        ]);
    }
    public function draft()
    {
        $this->published = false;
        $this->save();
    }

    public function published()
    {
        $this->published = true;
        $this->save();
    }

    public function view(): string
    {
        return view(self::PATH_TEMPLATES . $this->template)->render();
    }
}
