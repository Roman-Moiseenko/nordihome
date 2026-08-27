<?php
declare(strict_types=1);

namespace App\Modules\Discount\Infrastructure\Models;

use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name //Имя для внутреннего использования
 * @property string $description
 * @property string $condition_url //Ссылка на страницу с условиями и правилами
 * @property Carbon $start_at
 * @property Carbon $finish_at
 * @property bool $menu
 * @property bool $show_title //Показывать заголовок акции на карточках
 * @property string $title
 * @property bool $published //Опубликовать из черновиков. Опубликованные запускаются автоматически по Cron-у
 * @property bool $active // по Cron if ($start_at > time() && $published) $active = true;
 * @property string $slug  //По title, если существует, добавляем год
 * @property string $template
 * @property int $discount
 * @property string $color_class
 * @property string $position_class
 * @property string $text_tag
 * @property bool $show_tag
 * @property string $svg
 * @property bool $show_discount
 * @property Product[] $products
 */
class Promotion extends Model
{
    use ImageField;

    const string DRAFT = 'draft';
    const string WAITING = 'waiting';
    const string STARTED = 'started';
    const string FINISHED = 'finished';
    const array STATUSES = [
        self::DRAFT => 'Черновик',
        self::WAITING => 'В ожидании',
        self::STARTED => 'Запущена',
        self::FINISHED => 'Остановлена',
    ];

    const TYPE = 'Акция';

    //////////////////////////////////////////////
    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
        'published' => 'boolean',
        'active' => 'boolean',
        'show_tag' => 'boolean',
        'show_discount' => 'boolean',
    ];


    public $timestamps = false;

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
        'published',
        'active',
        'svg',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'active' => false,
            'published' => false,
            'description' => '',
            'title' => '',
        ]);
    }

    public function status(): string
    {
        if ($this->active) return self::STARTED; //'Активна';
        if (!$this->published) return self::DRAFT;
        if (!is_null($this->finish_at) && $this->finish_at->lt(now())) return self::FINISHED;
        if (empty($this->start_at) || $this->start_at->gte(now())) return self::WAITING;

        return self::WAITING;
        //throw new \DomainException('Неучтенная комбинация!!!');
    }

    public function isStarted(): bool
    {
        if (
            $this->active
            && $this->start_at->lte(now())
            && (is_null($this->finish_at) || $this->finish_at->gte(now()))
        ) return true;
        return false;
    }

    public function isFinished(): bool
    {
        if (!is_null($this->finish_at) && $this->finish_at->lt(now()) && !$this->active) return true;
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

    public function finish(): void
    {
        $this->active = false;
    }

    public function start(): void
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

    public function products(): BelongsToMany//: array
    {
        return $this->belongsToMany(
            Product::class, 'promotions_products',
            'promotion_id', 'product_id')->withPivot(['price']);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
