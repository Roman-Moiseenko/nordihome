<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use App\Trait\PictureTrait;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\select;

/**
 * @property int $id
 * @property int $category_id
 * @property int $group_id
 * @property string $name
 * @property AttributeGroup $group
 * @property Category[] $categories
 * @property int $type
 * @property AttributeVariant[] $variants
 * @property bool $multiple
 * @property bool $filter
 * @property bool $show_in
 * @property Photo $image
 * @property string $sameAs
 */
class Attribute extends Model
{
    const TYPE_STRING = 101;
    const TYPE_INTEGER = 103;
    const TYPE_BOOL = 102;
    const TYPE_VARIANT = 104;
    const TYPE_FLOAT = 105;
    const TYPE_DATE = 106;

    const ATTRIBUTES = [
        self::TYPE_STRING => 'Строка',
        self::TYPE_BOOL => 'Число',
        self::TYPE_INTEGER => 'Флажок',
        self::TYPE_VARIANT => 'Варианты',
        self::TYPE_FLOAT => 'Дробное',
        self::TYPE_DATE => 'Дата',
    ];

    //public array $variants;
    public $timestamps = false;
    public $thumbs = false;

    protected $fillable = [
        'name', 'type', 'multiple', 'filter', 'group_id', 'show_in',
    ];

    public static function register(string $name, int $group_id, int $type): self
    {
        return self::create([
            'name' => $name,
            'group_id' => $group_id,
            'type' => $type,
        ]);

    }

    public function group()
    {
        return $this->belongsTo(AttributeGroup::class, 'group_id', 'id');
    }

    /*    public function category()
        {
            return $this->belongsTo(Category::class, 'group_id', 'id');
        }
    */
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
        if (empty($this->image->file)) {
            return '/images/no-image.jpg';
        } else {
            return $this->image->getUploadUrl();
        }
    }

    public function addVariant(string $name, $file = null)
    {
        $variant = AttributeVariant::register($name);
        if (!empty($file)) $variant->image->newUploadFile($file);
        $this->variants()->save($variant);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'attributes_categories', 'attribute_id', 'category_id');
    }


    public function isCategory(Category $category): bool
    {
        foreach ($this->categories as $_category) {
            if ($_category->id == $category->id) return true;
        }
        return false;
    }

    public function isGroup(AttributeGroup $group): bool
    {
        return $this->group_id == $group->id;
    }

    public function isVariant(): bool
    {
        return $this->type == self::TYPE_VARIANT;
    }

    public function getUploadUrl(): string
    {
        if (!empty($this->image)) {
            return $this->image->getUploadUrl();
        }
        return '';
    }
}