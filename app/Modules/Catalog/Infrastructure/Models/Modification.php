<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property Product[] $products
 * @property Attribute[] $prod_attributes
 */
class Modification extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Атрибуты-варианты, задающие модификацию (pivot modification_attributes).
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            Attribute::class,
            'modification_attributes',
            'modification_id',
            'attribute_id',
            'id',
            'id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    /**
     * Товары-варианты внутри модификации (pivot modifications_products).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'modifications_products',
            'modification_id',
            'product_id',
            'id',
            'id'
        )
            ->withPivot(['id', 'is_primary']);
    }

    public function productByVariant(array $var): ?Product
    {
        foreach ($this->products as $product) {
            $values = ModificationProductValue::query()
                ->where('modification_product_id', $product->pivot->id)
                ->pluck('variant_id', 'attribute_id')
                ->all();

            if (empty(array_diff($values, $var))) {
                return $product;
            }
        }

        return null;
    }

    public function getVariations(bool $only_free = false): array
    {
        $result = [];
        $attributes = $this->prod_attributes;
        $count = count($attributes);

        if ($count == 1) {
            foreach ($attributes[0]->variants as $variant_1) {
                $result[] = [
                    $attributes[0]->id => $variant_1->id,
                ];
            }
        }
        if ($count == 2) {
            foreach ($attributes[0]->variants as $variant_1) {
                foreach ($attributes[1]->variants as $variant_2) {
                    $result[] = [
                        $attributes[0]->id => $variant_1->id,
                        $attributes[1]->id => $variant_2->id,
                    ];
                }
            }
        }
        if ($count == 3) {
            foreach ($attributes[0]->variants as $variant_1) {
                foreach ($attributes[1]->variants as $variant_2) {
                    foreach ($attributes[2]->variants as $variant_3) {
                        $result[] = [
                            $attributes[0]->id => $variant_1->id,
                            $attributes[1]->id => $variant_2->id,
                            $attributes[2]->id => $variant_3->id,
                        ];
                    }
                }
            }
        }

        if ($count > 3) {
            throw new \DomainException('Недопустимое количество вариантов для модификации товаров');
        }

        if ($only_free) { // Только свободные варианты
            foreach ($result as $key => $item) {
                if (!is_null($this->productByVariant($item))) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }

    public function isProduct(int $id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $id) {
                return true;
            }
        }

        return false;
    }

    public function isSale(): bool
    {
        foreach ($this->products as $product) {
            if ($product->isSale()) {
                return true;
            }
        }

        return false;
    }
}
