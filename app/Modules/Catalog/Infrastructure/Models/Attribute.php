<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Base\Traits\ImageField;
use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $category_id
 * @property int $group_id
 * @property string $name
 * @property AttributeGroup $group
 * @property Product[] $products
 * @property Category[] $categories
 * @property string $type
 * @property AttributeVariant[] $variants
 * @property bool $multiple
 * @property bool $filter
 * @property bool $show_in
 * @property string $sameAs
 */
class Attribute extends Model
{
    use ImageField;

    //public array $variants;
    public $timestamps = false;

    protected $casts = [
        'filter' => 'bool',
        'multiple' => 'bool',
        'show_in' => 'bool',
    ];

    protected $attributes = [
        'multiple' => false, 'filter' => true, 'show_in' => true,
    ];

    protected $fillable = [
        'name',
        'type',
        'multiple',
        'filter',
        'group_id',
        'show_in',
    ];

    public static function register(string $name, int $group_id, string $type): self
    {
        return self::create([
            'name' => $name,
            'group_id' => $group_id,
            'type' => $type,
        ]);
    }

    public function isVariant(): bool
    {
        return $this->type == AttributeType::TYPE_VARIANT;
    }

    public function isBool(): bool
    {
        return $this->type == AttributeType::TYPE_BOOL;
    }

    public function isNumeric(): bool
    {
        return $this->type == AttributeType::TYPE_INTEGER || $this->type == AttributeType::TYPE_FLOAT;
    }

    public function isString(): bool
    {
        return $this->type == AttributeType::TYPE_STRING;
    }

    public function isDate(): bool
    {
        return $this->type == AttributeType::TYPE_DATE;
    }

    public function isValue($value): bool
    {
        foreach ($this->variants as $variant) {
            if ($variant->name == $value) return true;
        }
        return false;
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class, 'group_id', 'id');
    }
/*
        public function category()
        {
            return $this->belongsTo(Category::class, 'group_id', 'id');
        }
    */

    public function variants()
    {
        return $this->hasMany(AttributeVariant::class, 'attribute_id', 'id');
    }

    public function addVariant(string $name): AttributeVariant
    {
        $variant = AttributeVariant::register($name);
        $this->variants()->save($variant);
        $variant->refresh();
        return $variant;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'attributes_categories', 'attribute_id', 'category_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'attributes_products',
            'attribute_id', 'product_id', 'id', 'id')->withPivot('value');
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

    public function ValueJSON():? string
    {
        if (!is_null($this->pivot)) return $this->pivot->value;
        return null;
    }

    public function Value()
    {
        if (!is_null($this->pivot)) return json_decode($this->pivot->value, true);
        return null;
    }




    public function getUploadUrl(): string
    {
        if (!empty($this->image)) {
            return $this->image->getUploadUrl();
        }
        return '';
    }

    public function getVariant(int $id): AttributeVariant
    {
        foreach ($this->variants as $variant) {
            if ((int)$variant->id == $id) return $variant;
        }
        throw new \DomainException('(get) Не найден вариант id = ' . $id . ' атрибута ' . $this->name );
    }

    public function findVariant(string $name): AttributeVariant
    {
        foreach ($this->variants as $variant) {
            if ($variant->name == $name) return $variant;
        }
        throw new \DomainException('(find) Не найден вариант ' . $name . ' атрибута ' . $this->name);
    }

    public function typeText(): string
    {
        return AttributeType::ATTRIBUTES[$this->type];
    }

}
