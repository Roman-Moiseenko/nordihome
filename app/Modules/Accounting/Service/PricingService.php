<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\PricingHasCompleted;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Admin\Entity\Admin;

use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PricingService
{

    public function create(int $arrival_id = null): PricingDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();

        $pricing = PricingDocument::register($staff->id);
        if (!is_null($arrival_id)) {
            $pricing->arrival_id = $arrival_id;
            $pricing->save();
        }
        return $pricing;
    }

    public function create_arrival(ArrivalDocument $arrival):? PricingDocument
    {
        if (!$arrival->isCompleted()) throw new \DomainException('Поступление не проведено. Создать установку цен невозможно!');
        $pricing = $this->create($arrival->id);
        foreach ($arrival->arrivalProducts as $arrivalProduct) {
            $this->add($pricing, $arrivalProduct->product_id, $arrivalProduct->getCostRu());
        }
        return $pricing;
    }

    public function destroy(PricingDocument $pricing): void
    {
        if ($pricing->isCompleted()) throw new \DomainException('Документ проведен, удалить нельзя');
        $pricing->delete();
    }

    public function add(PricingDocument $pricing, int $product_id, int $cost = null): PricingDocument
    {
        if ($pricing->isCompleted()) throw new \DomainException('Документ проведен, менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($pricing->isProduct($product_id)) {
            flash('Товар ' . $product->name . ' уже добавлен в документ', 'warning');
            return $pricing;
        }

        $pricingProduct = PricingProduct::new(
            $product->id,
            $cost ?? $product->getPriceCost(), //Если передана закупочная цена $cost
            $product->getPriceRetail(),
            $product->getPriceBulk(),
            $product->getPriceSpecial(),
            $product->getPriceMin(),
            $product->getPricePre()
        );
        $pricing->pricingProducts()->save($pricingProduct);
        $pricing->refresh();
        return $pricing;
    }

    public function add_products(PricingDocument $pricing, string $textarea): PricingDocument
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add($pricing, $product->id);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
        return $pricing;
    }

    public function set(PricingProduct $product, array $request): void
    {

        if (!empty($request['price_cost'])) $product->price_cost = (float)$request['price_cost'];
        if (!empty($request['price_retail'])) $product->price_retail = (float)$request['price_retail'];
        if (!empty($request['price_bulk'])) $product->price_bulk = (float)$request['price_bulk'];
        if (!empty($request['price_special'])) $product->price_special = (float)$request['price_special'];
        if (!empty($request['price_min'])) $product->price_min = (float)$request['price_min'];
        if (!empty($request['price_pre'])) $product->price_pre = (float)$request['price_pre'];

        $product->save();
    }

    public function remove_item(PricingProduct $item): void
    {
        $item->delete();
    }

    public function completed(PricingDocument $pricing): void
    {
        if ($pricing->isCompleted()) throw new \DomainException('Документ уже проведен');
        DB::transaction(function () use ($pricing) {
            $pricing->number = PricingDocument::where('number', '<>', null)->count() + 1;
            $pricing->completed = true;
            $pricing->save();
            $pricing->refresh();
            foreach ($pricing->pricingProducts as $pricingProduct) {
                $product = $pricingProduct->product;
                $founded = 'Установка цен № ' . $pricing->htmlNum() . ' от ' . $pricing->htmlDate();
                //Сохранять, Если значения отличаются
                if ($product->getPriceCost() != $pricingProduct->price_cost)
                    $product->pricesCost()->create([
                        'value' => $pricingProduct->price_cost,
                        'founded' => $founded,
                    ]);
                if ($product->getPriceRetail() != $pricingProduct->price_retail)
                    $product->pricesRetail()->create([
                        'value' => $pricingProduct->price_retail,
                        'founded' => $founded,
                    ]);
                if ($product->getPriceBulk() != $pricingProduct->price_bulk)
                    $product->pricesBulk()->create([
                        'value' => $pricingProduct->price_bulk,
                        'founded' => $founded,
                    ]);
                if ($product->getPriceSpecial() != $pricingProduct->price_special)
                    $product->pricesSpecial()->create([
                        'value' => $pricingProduct->price_special,
                        'founded' => $founded,
                    ]);
                if ($product->getPriceMin() != $pricingProduct->price_min)
                    $product->pricesMin()->create([
                        'value' => $pricingProduct->price_min,
                        'founded' => $founded,
                    ]);
                if ($product->getPricePre() != $pricingProduct->price_pre)
                    $product->pricesPre()->create([
                        'value' => $pricingProduct->price_pre,
                        'founded' => $founded,
                    ]);
            }

            event(new PricingHasCompleted($pricing));
        });
    }

    public function copy(PricingDocument $pricing)
    {
        $copy = $this->create();

        foreach ($pricing->pricingProducts as $pricingProduct) {
            $this->add($copy, $pricingProduct->product_id);
        }

        return $copy;
    }

}
