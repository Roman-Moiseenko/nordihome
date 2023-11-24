<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Entity\Observer;
use App\Entity\Photo;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\IWidgetHome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $condition_url //Ссылка на страницу с условиями и правилами
 * @property int $start_at
 * @property int $finish_at
 * @property Photo $image
 * @property Photo $icon
 * @property bool $menu
 * @property string $title
 * @property bool $published //Опубликовать из черновиков. Опубликованные запускаются автоматически по Cron-у
 * @property bool $active // по Cron if ($start_at > time() && $published) $active = true;
 * @property string $slug  //По title, если существует, добавляем год
 * @property Group[] $groups
 */
class Promotion extends Model implements IWidgetHome
{

    //Позволяем себя слушать
    private array $observers = [];
    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }
    public function detach(Observer $observer) {
        $key = array_search($observer, $this->observers, true);
        if($key !== false) {
            unset($this->observers[$key]);
        }
    }
    public function notify() {
        foreach($this->observers as $observer) {
            $observer->update($this);
        }
    }
    //////////////////////////////////////////////

    protected $fillable = [
        'name', 'title', 'slug', 'finish_at', 'start_at'
    ];

    public static function register(string $name, string $title,  int $finish_at, bool $menu = false, int $start_at = null, string $slug = ''): self
    {
        return self::create([
            'name' => $name,
            'title' => $title,
            'slug' => empty($slug) ? Str::slug($title) : $slug,
            'finish_at' => $finish_at,
            'menu' => $menu,
            'start_at' => $start_at,
            'published' => false,
        ]);
    }

    public function finish()
    {
        $this->active = false;
        $this->update(['active' => $this->active]);
        //TODO Добавить в Очередь событие - Акция $name закончилась
    }

    public function start()
    {
        $this->active = true;
        $this->update(['active' => $this->active]);
        //TODO Добавить в Очередь событие - Акция $name началась
    }

    public function published(): void
    {
        $this->published = true;
        $this->update(['published' => $this->published]);
        //TODO Добавить в Очередь событие - Акция $name запущена и скоро начнется
    }

    public function ProductsForWidget()
    {
        // TODO: Implement ProductsForWidget() method.
    }

    public function isGroup(int $id): bool
    {
        foreach ($this->groups as $group) {
            if ($group->id == $id) return true;
        }
        return false;
    }


    public function groups()
    {
        return $this->belongsToMany(
            Group::class, 'promotions_groups',
            'promotion_id', 'group_id')->withPivot('discount');
    }

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', '=','image')->withDefault();
    }
    public function icon()
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', '=', 'icon')->withDefault();
    }

    public function getImage(): string
    {
        if (empty($this->image->file)) {
            return '/images/default-catalog.jpg';
        } else {
            return $this->image->getUploadUrl();
        }
    }


    public function getIcon(): string
    {
        if (empty($this->icon->file)) {
            return '/images/default-catalog.png';
        } else {
            return $this->icon->getUploadUrl();
        }
    }

    public function isPublished()
    {
        return $this->published;
    }
}
