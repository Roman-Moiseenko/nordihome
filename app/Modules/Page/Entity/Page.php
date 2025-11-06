<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
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
 * @property string $text
 * @property bool $menu
 * @property bool $published
 * @property int $sort
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Meta $meta
 * @property Page $parent
 */
class Page extends Model
{
    use ImageField, IconField;

    protected $attributes = [
        'meta' => '{}',
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
        'published',
        'text'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'meta' => MetaCast::class,
    ];

    public static function register(string $name, string $slug,
                                    string $title, string $description, string $template, bool $menu, int $parent_id = null): self
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
            'menu' => $menu,
            'published' => false,
            'text' => '',
        ]);
    }

    public function setText(string $text): void
    {
        $this->text = $text;
        $this->save();
    }

    public function draft(): void
    {
        $this->published = false;
        $this->save();
    }

    public function published(): void
    {
        $this->published = true;
        $this->save();
    }

    /**
     * @throws \Throwable
     */
    public function view(): string
    {
            $this->text = Template::renderClasses($this->text);
            $url_page = route('shop.page.view', $this->slug);
            //TODO На будущее
            // $this->text = Template::renderFromText('promotion', $this->text);

            return view(
                Template::blade('page') . $this->template,
                ['page' => $this, 'title' => $this->meta->title, 'description' => $this->meta->description, 'url_page' => $url_page])
                ->render();

    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('published', true);
    }
}
