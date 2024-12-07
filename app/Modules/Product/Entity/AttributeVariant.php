<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property Attribute $attribute
 * @property string $name
 * @property string $slug
 */
class AttributeVariant extends Model
{
    use ImageField;

    public $timestamps = false;
    public $thumbs = false;
    protected $fillable = [
        'name', 'slug', 'attribute_id'
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    public static function register(string $name): self
    {
        return self::make([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

}
