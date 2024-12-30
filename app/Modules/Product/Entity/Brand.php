<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property string $sameas_json
 * @property Product[] $products
 */
class Brand extends Model
{
    use ImageField;

    const DEFAULT = 1;
    const IKEA = 'Икеа';

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

    public function products(): HasMany
    {
       // return null;
       return $this->hasMany(Product::class, 'brand_id', 'id');
    }

    public static function boot()
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
}
