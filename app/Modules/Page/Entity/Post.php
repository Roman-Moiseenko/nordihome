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
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $template
 * @property string $description
 * @property PostCategory $category
 *
 */
class Post extends RenderPage
{
    use ImageField, IconField;

    protected $fillable = [
        'name',
        'slug',
        'template',
    ];

    public static function new(string $name, string $template): static
    {
        return self::make([
            'name' => $name,
            'template' => $template,
            'slug' => Str::slug($name),
        ]);
    }
/*
    public function published(): void
    {
        if ($this->published_at == null) $this->published_at = now();
        $this->published = true;
    }

    public function draft(): void
    {
        $this->published = false;
    }
*/
    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }


    /*
    public function view(): string
    {
        $this->text = Template::renderClasses($this->text);
        $url_page = route('shop.post.view', $this->slug);


        return view(
            Template::blade('post') . $this->template,
            ['post' => $this, 'title' => $this->meta->title, 'description' => $this->meta->description, 'url_page' => $url_page])
            ->render();

    }

    public function scopeActive($query)
    {
        return $query->where('published', true);
    }
*/
}
