<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property Attribute $attribute
 * @property string $name
 * @property string $slug
 * @property Photo $image
 */
class AttributeVariant extends Model
{
    public $timestamps = false;
    public $thumbs = false;
    protected $fillable = [
        'name', 'slug', 'attribute_id'
    ];

    public function attribute()
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

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }

    public function getImage(): string
    {
        if (empty($this->image->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->image->getUploadUrl();
        }
    }
}
