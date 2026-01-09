<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property bool $menu
 * @property bool $show_title //Показывать заголовок акции на карточках
 * @property string $title
 * @property bool $published //Опубликовать из черновиков. Опубликованные запускаются автоматически по Cron-у
 * @property bool $active // по Cron if ($start_at > time() && $published) $active = true;
 * @property string $slug  //По title, если существует, добавляем год
 * @property string $template
 * @property int $discount
 * @property Product[] $products
 */
class Promotion extends Model
{
    use ImageField, IconField;

    const int STATUS_DRAFT = 101;
    const int STATUS_WAITING = 102;
    const int STATUS_STARTED = 103;
    const int STATUS_FINISHED = 104;
    const array STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_WAITING => 'В ожидании',
        self::STATUS_STARTED => 'Запущена',
        self::STATUS_FINISHED => 'Завершена',
    ];

    const TYPE = 'Акция';

    //////////////////////////////////////////////
    protected $casts = [
        'start_at' => 'datetime',
        'finish_at' => 'datetime',
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

    public function status(): int
    {
        if ($this->active) return self::STATUS_STARTED; //'Активна';
        if (!$this->published) return self::STATUS_DRAFT;
        if (!is_null($this->finish_at) && $this->finish_at->lt(now())) return self::STATUS_FINISHED;
        if (empty($this->start_at) || $this->start_at->gte(now())) return self::STATUS_WAITING;

        return self::STATUS_WAITING;
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
