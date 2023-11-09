<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Repository\AttributeRepository;
use Illuminate\Http\Request;

class ModificationService
{
    private AttributeRepository $attributes;

    public function __construct(AttributeRepository $attributes)
    {
        $this->attributes = $attributes;
    }

    public function create(Request $request)
    {
        $attributes = [];
        foreach ($request['attribute_id'] as $id) {
            if ($_attr = $this->attributes->existAndGet((int)$id)) $attributes[] = $_attr;
        }
        $modification = Modification::register($request['name'], (int)$request['product_id'], $attributes);

        return $modification;
    }

    public function update(Request $request, Modification $modification): Modification
    {
        $attributes = [];
        foreach ($request['attributes'] as $id) {
            if ($_attr = $this->attributes->existAndGet((int)$id)) $attributes[] = $_attr;
        }

        $modification->name = $request['name'];
        if ((int)$request['product_id'] !== $modification->base_product_id || !empty(array_diff($attributes, $modification->prod_attributes))) {
            $modification->products()->detach();
            $modification->base_product_id = (int)$request['product_id'];
            $modification->prod_attributes = $attributes;
        }
        $modification->save();
        $modification->refresh();
        return $modification;
    }

    public function set_modifications(Request $request, Modification $modification): Modification
    {
        //TODO Тестировать
        foreach ($request['products'] as $key => $product_id) {
            $values = [];
            foreach ($request['attributes'][$key] as $j => $attribute_id) {
                $values[$attribute_id] = $request['values'][$key][$j];
            }

            $modification->products()->attach($product_id, ['values_json' => json_encode($values)]);
        }
        $modification->push();
        $modification->refresh();
        return $modification;
    }

    public function delete(Modification $modification)
    {
        $modification->products()->detach();
        Modification::destroy($modification->id);
    }



}
