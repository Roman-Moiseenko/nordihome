<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name //Наименование группы по первому товару
 * @property int $base_product_id //Базовый продукт unique
 * @property string $attributes_json
 * @property Product[] $products
 * @property Attribute[] $prod_attributes
 */
class Modification extends Model
{
    public $timestamps = false;

    public array $prod_attributes = [];
    protected $fillable = [
        'name',
        'base_product_id',
        'attributes_json',
    ];

    public static function register(string $name, int $base_product_id, array $attributes): self
    {
        if (empty($attributes)) throw new \DomainException('Не заданы атрибуты');
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->type !== Attribute::TYPE_VARIANT) {
                throw new \DomainException('Неверный тип атрибутов. Должен быть Вариант!');
            }
        }

        $modification = self::make([
            'name' => $name,
            'base_product_id' => $base_product_id,
        ]);
        $modification->prod_attributes = $attributes;
        $modification->save();
        return $modification;
    }

    public function base_product()
    {
       return $this->belongsTo(Product::class, 'base_product_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'modifications_products', 'modification_id', 'product_id', 'id', 'id')
            ->withPivot('values_json');
    }

    protected static function boot()
    {
        parent::boot();
        self::saving(function (Modification $modification) {
            $modification->attributes_json = json_encode(
                array_map(function (Attribute $item) {
                    return $item->id;
                }, $modification->prod_attributes)
            );
        });

        self::retrieved(function (Modification $modification) {
            $modification->prod_attributes = array_map(function ($item) {
                return Attribute::find($item);
            }, json_decode($modification->attributes_json));
        });
    }

    public function getVariations(): array
    {
        $result = [];
        $count = count($this->prod_attributes);

        if ($count == 1) {
            foreach ($this->prod_attributes[0]->variants as $variant_1) {
                $result[] = [
                    $this->prod_attributes[0]->id => $variant_1->id,
                    ];
            }
        }
        if ($count == 2) {
            foreach ($this->prod_attributes[0]->variants as $variant_1) {
                foreach ($this->prod_attributes[1]->variants as $variant_2) {
                    $result[] = [
                        $this->prod_attributes[0]->id => $variant_1->id,
                        $this->prod_attributes[1]->id => $variant_2->id,
                    ];
                }
            }
        }
        if ($count == 3) {
            foreach ($this->prod_attributes[0]->variants as $variant_1) {
                foreach ($this->prod_attributes[1]->variants as $variant_2) {
                    foreach ($this->prod_attributes[2]->variants as $variant_3) {
                        $result[] = [
                            $this->prod_attributes[0]->id => $variant_1->id,
                            $this->prod_attributes[1]->id => $variant_2->id,
                            $this->prod_attributes[2]->id => $variant_3->id,
                        ];
                    }
                }
            }
        }

        if ($count > 3) {
            throw new \DomainException('Недопустимое количество вариантов для модификации товаров');
        }
        return $result;
    }

    public function isProduct(int $id): bool
    {
        foreach ($this->products as $product) {
            if ($product->id == $id) return true;
        }
        return false;
    }
}
