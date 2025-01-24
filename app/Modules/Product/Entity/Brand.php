<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $currency_id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property string $sameas_json
 * @property Product[] $products
 * @property string $parser_class ... класс Парсер товаров
 * @property Currency $currency
 */
class Brand extends Model
{
    use ImageField;

    const DEFAULT = 1;
    const IKEA = 'Икеа';
    const NB = 'New Balance';

    private array $sameAs = [];
    protected $fillable = [
        'name',
        'description',
        'url',
    ];

    protected $hidden = [
        'sameas_json',
    ];
    public $timestamps = false;

    public static function register($name, $description = '', $url = ''): self
    {
        return static::create([
            'name' => $name,
            'description' => $description,
            'url' => $url,
        ]);
    }

    public function setSameAs($sameAs): void
    {
        $this->sameAs = $sameAs ?? [];
    }

    public function getSameAs(): array
    {
        return $this->sameAs;
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function products(): HasMany
    {
       return $this->hasMany(Product::class, 'brand_id', 'id');
    }

    public static function boot(): void
    {
        parent::boot();
        self::saving(function (Brand $object) {
            $object->sameas_json = json_encode($object->sameAs);
        });

        self::retrieved(function (Brand $object) {
            $object->sameAs = json_decode($object->sameas_json);
        });
    }

    public static function IkeaID(): int
    {
        return self::where('name', self::IKEA)->first()->id;
    }

    public static function NbID(): int
    {
        return self::where('name', self::NB)->first()->id;
    }
}
