<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use App\Trait\PictureTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property AttributeGroup $group
 * @property Category $category
 * @property int $type
 * @property AttributeVariant[] $variants
 * @property bool $multiple
 * @property bool $filter
 * @property Photo $image
 *
 */
class Attribute extends Model
{
    const TYPE_INTEGER = 101;
    const TYPE_BOOL = 102;
    const TYPE_STRING = 103;
    const TYPE_VARIANT = 104;
    const TYPE_FLOAT = 105;
    const TYPE_DATE = 106;

    //public array $variants;
    public $timestamps = false;
    public $thumbs = false;

    protected $fillable = [
        'name', 'type', 'multiple', 'filter', 'group_id', 'category_id',
    ];

    public static function register(string $name, AttributeGroup $group, Category $category, int $type): self
    {
        $attribute = self::create([
            'name' => $name,
            'group_id' => $group->id,
            'category_id' => $category->id,
            'type' => $type,
        ]);

        return $attribute;
    }

    public function group()
    {
        return $this->belongsTo(AttributeGroup::class, 'group_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'group_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(AttributeVariant::class, 'attribute_id', 'id');
    }

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable');
    }

    public function getImage()
    {
        if (empty($this->image)) return '';
        return $this->image->getUploadUrl();
    }

    public function addVariant(AttributeVariant $variant)
    {

        $this->variants()->save($variant);
    }
}
