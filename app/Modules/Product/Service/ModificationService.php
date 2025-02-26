<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\EquivalentProduct;
use App\Modules\Product\Entity\GroupProduct;
use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\AttributeRepository;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\Request;

class ModificationService
{
    private AttributeRepository $attributes;

    public function __construct(AttributeRepository $attributes)
    {
        $this->attributes = $attributes;
    }

    public function create(Request $request): Modification
    {
        $attributes = [];
        foreach ($request['attributes'] as $id) {
            if ($_attr = $this->attributes->existAndGet((int)$id)) $attributes[] = $_attr;
        }
        $modification = Modification::register(
            $request->string('name')->trim()->value(),
            $request->integer('product_id'),
            $attributes);
        $product = Product::find($request->integer('product_id'));
        $this->attachProduct($modification, $product);
        return $modification;
    }

    public function rename(Request $request, Modification $modification): void
    {
        $modification->name = $request->string('name')->trim()->value();
        $modification->save();
    }

    public function delete(Modification $modification): void
    {
        $modification->products()->detach();
        Modification::destroy($modification->id);
    }

    public function addProduct(Request $request, Modification $modification): void
    {
        $product_id = $request->integer('product_id');
        $product = Product::find($product_id);
        $this->attachProduct($modification, $product);
    }

    private function attachProduct(Modification $modification, Product $product): void
    {
        $values = [];
        foreach ($modification->prod_attributes as $attribute) {
            if (is_null($product->getProdAttribute($attribute->id)))
                throw new \DomainException('Товар ' . $product->name . ' не имеет атрибута из модификации');
            if (is_array($product->Value($attribute->id))) {
                $values[$attribute->id] = (int)$product->Value($attribute->id)[0];
            } else {
                $values[$attribute->id] = $product->Value($attribute->id);
            }
        }
        $modification->products()->attach($product->id, ['values_json' => json_encode($values)]);
    }

    public function delProduct(Request $request, Modification $modification): void
    {
        $product_id = $request->integer('product_id');
        $modification->products()->detach($product_id);
    }

    public function setBase(Modification $modification, int $product_id): void
    {
        $base = $modification->base_product;
        $product = Product::find($product_id);
        $base_id = $modification->base_product_id;

        //Если базовый Парсер, а новый нет - то переносим парсер на новый
        //dd([$base->parser,$product->parser ]);
        if (!is_null($base->parser) && is_null($product->parser)) {
            $parser = $base->parser;
            $parser->product_id = $product->id;
            $parser->save();
        }
        set_time_limit(300);
        if ($product->gallery->count() == 0) {
            foreach ($base->gallery as $photo) {
                $product->copyImage($photo);
                $photo->delete();
            }
        }



        if (!is_null($base->equivalent)) {
            $equivalent = $base->equivalent;
            $equivalent->products()->attach($product_id);
            $equivalent->products()->detach($base_id);
        }

        foreach ($base->groups as $group) {
            $group->products()->attach($product_id);
            $group->products()->detach($base_id);
        }

        $modification->base_product_id = $product->id;
        $modification->save();
        set_time_limit(30);
    }

}
