<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $modification_id
 * @property int $product_id
 * @property string $values_json //['attribute_id' => 'variant_id', 'attribute_id' => 'variant_id']
 * @property Modification $modification
 */
class ModificationProduct extends Model
{
    public array $values = [];
    public $timestamps = false;
    protected $table = 'modifications_products';

    public function register(int $modification_id, int $product_id, array $variants): self
    {
        $element = self::make([
            'modification_id' => $modification_id,
            'product_id' => $product_id,
        ]);
        $element->values = $variants;
        $element->save();
        return $element;
    }

    public function modification()
    {
        return $this->belongsTo(Modification::class, 'modification_id', 'id');
    }


}
