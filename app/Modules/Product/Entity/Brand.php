<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Trait\PictureTrait;
use App\UseCases\Uploads\UploadsDirectory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $url
 * @property string $sameas_json
 * @property string $photo
 */
class Brand extends Model implements UploadsDirectory
{
    const DEFAULT = 1;

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

    public function getUploadsDirectory(): string
    {
        return 'uploads/product/brands/' . $this->id . '/';
    }

    public static function register($name, $description = '', $url = '', array $sameAs = []): self
    {
        $brand = static::create([
            'name' => $name,
            'description' => $description,
            'url' => $url,
        ]);
        $brand->sameAs = $sameAs;
        return $brand;
    }

    public function setSameAs(array $sameAs): void
    {
        $this->sameAs = $sameAs;
    }

    public function getSameAs(): array
    {
        return $this->sameAs;
    }

    public function setPhoto(string $file): void
    {
        $this->photo = $file;
    }

    public function products(): HasMany
    {
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
}
