<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Page\Entity\DataWidget;
use App\Modules\Page\Entity\DataWidgetInterface;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $id
 * @property string $name //Имя для внутреннего использования
 * @property string $description
 * @property string $condition_url //Ссылка на страницу с условиями и правилами
 * @property Carbon $start_at
 * @property Carbon $finish_at
 * @property Photo $image
 * @property \App\Modules\Base\Entity\Photo $icon
 * @property bool $menu
 * @property bool $show_title //Показывать заголовок акции на карточках
 * @property string $title
 * @property bool $published //Опубликовать из черновиков. Опубликованные запускаются автоматически по Cron-у
 * @property bool $active // по Cron if ($start_at > time() && $published) $active = true;
 * @property string $slug  //По title, если существует, добавляем год
 * @property int $discount
 * @property Product[] $products
 */
class Promotion extends Model implements DataWidgetInterface
{

    const STATUS_DRAFT = 101;
    const STATUS_WAITING = 102;
    const STATUS_STARTED = 103;
    const STATUS_FINISHED = 104;

    const TYPE = 'Акция';

  /*  private array $observers = [];


    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
*/
    //////////////////////////////////////////////
    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
    ];


    public $timestamps = false;
    protected $attributes = [
        'published' => false,
        'active' => false,
        'description' => '',
    ];

    protected $fillable = [
        'name',
        'title',
        'slug',
        'finish_at',
        'start_at',
        'show_title',
        'description',
        'menu',
        'condition_url',
        'discount',
        //'published', 'active',
    ];

    public static function register(string $name, string $title, $finish_at, bool $menu = false, $start_at = null, string $slug = null): self
    {
        return self::create([
            'name' => $name,
            'title' => $title,
            'slug' => empty($slug) ? Str::slug($title) : $slug,
            'finish_at' => $finish_at,
            'menu' => $menu,
            'start_at' => $start_at,
        ]);
    }

    public function status()
    {
        if ($this->active) return self::STATUS_STARTED; //'Активна';
        if (!$this->published) return self::STATUS_DRAFT;
        if ($this->finish_at->lt(now())) return self::STATUS_FINISHED;
        if (empty($this->start_at) || $this->start_at->gte(now())) return self::STATUS_WAITING;

        return self::STATUS_WAITING;
        //throw new \DomainException('Неучтенная комбинация!!!');
    }

    public function isStarted(): bool
    {
        if ($this->active && $this->start_at->lte(now()) && $this->finish_at->gte(now())) return true;
        return false;
    }

    public function isFinished(): bool
    {
        if ($this->finish_at->lt(now()) && !$this->active) return true;
        return false;
    }

    public function isWaiting(): bool
    {
        if (
            $this->published &&
            (empty($this->start_at) || $this->start_at->gte(now()))
        ) return true;
        return false;
    }

    public function isDraft(): bool
    {
        return !$this->published;
    }

    public function finish()
    {
        $this->active = false;
    }

    public function start()
    {
        $this->active = true;
    }

    public function published(): void
    {
        $this->published = true;
    }

    public function draft(): void
    {
        $this->published = false;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $product_id) return true;
        }
        return false;
    }

    public function isPublished(): bool
    {
        return $this->published == true;
    }

    public function countProducts(): int
    {
        return $this->products()->count();
    }

    public function products()//: array
    {
        return $this->belongsToMany(
            Product::class, 'promotions_products',
            'promotion_id', 'product_id')->withPivot('price');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Photo::class, 'imageable')->where('type', '=', 'image')->withDefault();
    }

    public function icon(): MorphOne
    {
        return $this->morphOne(\App\Modules\Base\Entity\Photo::class, 'imageable')->where('type', '=', 'icon')->withDefault();
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

    public function getDataWidget(array $params = []): DataWidget
    {
        $data = new DataWidget();
        $data->image = $this->image;
        $data->title = $this->title;
        $data->url = route('shop.promotion.view', $this->slug);
        $data->items = array_map(function (Product $product) {
            return [
                'image' => $product->photo,
                'url' => route('shop.product.view', $product->slug),
                'title' => $product->getName(),
                'price' => $product->getPrice(),
                'discount' => $product->pivot->price,
                'count' => $product->getCountSell(),
            ];
        }, $this->products()->getModels());
        return $data;
    }

}
