<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Observer;
use App\Entity\Photo;
use App\Modules\Product\IWidgetHome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property int $start_at
 * @property int $finish_at
 * @property Photo $image
 * @property bool $menu
 * @property string $title
 * @property bool $published // по Cron if $start_at > time() $published = true;
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
        $this->published = false;
        $this->update(['published' => $this->published]);
        //TODO Добавить в Очередь событие - Акция $name закончилась
    }

    public function published(): void
    {
        $this->published = true;
        $this->update(['published' => $this->published]);
        //TODO Добавить в Очередь событие - Акция $name началась
    }

    public function ProductsForWidget()
    {
        // TODO: Implement ProductsForWidget() method.
    }
}
