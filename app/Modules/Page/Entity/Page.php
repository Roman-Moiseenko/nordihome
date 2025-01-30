<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

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
 * @property Page $parent
 */
class Page extends Model
{

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

    public function setText(string $text)
    {
        $this->text = $text;
        $this->save();
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
        $this->text = Widget::renderFromText($this->text); //Рендерим виджеты в тексте
        $this->text = Banner::renderFromText($this->text); //Рендерим баннеры в тексте

       // $repository = app()->make(WebRepository::class);
       // $breadcrumb = $repository->getBreadcrumbModel($this); //Хлебные крошки - image, текст
    /*    $class = strtolower(class_basename(static::class)); //Класс вызвавший
        if (empty($this->template)) {
            $template = 'web.' .$class . '.show'; //Базовый шаблон системы
        } else {
            $template = 'web.templates.' . $class . '.' . $this->template; //Выбранный шаблон
        }
*/
        return view(Template::blade('page') . $this->template, ['page' => $this, 'title' => $this->title, 'description' => $this->description])->render();
        //dd($this);
     /*   return view($template,
            [
                $class => $this,
                'meta' => $this->meta,
                'breadcrumb' => $breadcrumb,
            ]
        )->render();*/
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
