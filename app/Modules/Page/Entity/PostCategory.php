<?php

namespace App\Modules\Page\Entity;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $template
 * @property string $slug
 * @property string $post_template
 * @property int $paginate
 * @property Meta $meta
 * @property Post[] $posts
 */
class PostCategory extends Model
{
    use ImageField, IconField;

    public $timestamps = false;
    protected $attributes = [
        'meta' => '{}',
    ];
    protected $casts = [
        'meta' => MetaCast::class,
    ];
    protected $fillable = [
        'name',
        'slug',
        'template',
    ];

    public static function register(string $name, string $slug, string $template): self
    {
        return self::create([
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'template' => $template,
        ]);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id', 'id')->orderByDesc('published_at');
    }

    /**
     * @throws \Throwable
     */
    public function view(): string
    {
        $this->text = Template::renderClasses($this->text);
        $url_page = route('shop.posts.view', $this->slug);

        $posts = $this->posts()->paginate($this->paginate ?? 20);

        return view(
            Template::blade('posts') . $this->template,
            ['category' => $this, 'posts' => $posts,
                'title' => $this->meta->title, 'description' => $this->meta->description, 'url_page' => $url_page])
            ->render();
    }

}
