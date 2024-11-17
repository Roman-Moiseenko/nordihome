<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\PricingHasCompleted;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Admin\Entity\Admin;

use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
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



    public function destroy(PricingDocument $pricing): void
    {
        if ($pricing->isCompleted()) throw new \DomainException('Документ проведен, удалить нельзя');
        $pricing->delete();
    }

    public function addProduct(PricingDocument $pricing, int $product_id): void
    {
        if ($pricing->isCompleted()) throw new \DomainException('Документ проведен, менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($pricing->isProduct($product_id))
            throw new \DomainException('Товар ' . $product->name . ' уже добавлен в документ');

        $pricingProduct = PricingProduct::new(product_id: $product->id);
        $pricing->products()->save($pricingProduct);
        $pricing->refresh();
    }

    public function addProducts(PricingDocument $pricing, array $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($pricing, $_product->id);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(PricingProduct $product, array $request): void
    {
        if (!empty($request['price_cost'])) $product->price_cost = (float)$request['price_cost'];
        if (!empty($request['price_retail'])) $product->price_retail = (float)$request['price_retail'];
        if (!empty($request['price_bulk'])) $product->price_bulk = (float)$request['price_bulk'];
        if (!empty($request['price_special'])) $product->price_special = (float)$request['price_special'];
        if (!empty($request['price_min'])) $product->price_min = (float)$request['price_min'];
        if (!empty($request['price_pre'])) $product->price_pre = (float)$request['price_pre'];

        $product->save();
    }

    public function delProduct(PricingProduct $item): void
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
                if ($pricingProduct->price_cost == 0 ||
                    $pricingProduct->price_retail == 0 ||
                    $pricingProduct->price_bulk == 0 ||
                    $pricingProduct->price_special == 0 ||
                    $pricingProduct->price_min == 0 ||
                    $pricingProduct->price_pre == 0
                ) throw new \DomainException('Не все цены заполнены');

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
            $this->addProduct($copy, $pricingProduct->product_id);
        }
        return $copy;
    }

    public function work(PricingDocument $pricing): void
    {
        if (!is_null(PricingDocument::where('created_at', '>', $pricing->created_at)
            ->completed()
            ->get()))
            throw new \DomainException('Существуют более поздние проведенные документы');

        DB::transaction(function () use ($pricing) {
            foreach ($pricing->products as $product) {
                $bulk = $product->product->pricesBulk()->skip(0)->first();
                if (!is_null($bulk)) $bulk->delete();

                $cost = $product->product->pricesCost()->skip(0)->first();
                if (!is_null($cost)) $cost->delete();

                $min = $product->product->pricesMin()->skip(0)->first();
                if (!is_null($min)) $min->delete();

                $pre = $product->product->pricesPre()->skip(0)->first();
                if (!is_null($pre)) $pre->delete();

                $retail = $product->product->pricesRetail()->skip(0)->first();
                if (!is_null($retail)) $retail->delete();

                $special = $product->product->pricesSpecial()->skip(0)->first();
                if (!is_null($special)) $special->delete();
            }
            $pricing->work();
        });
    }

    public function setInfo(PricingDocument $pricing, Request $request): void
    {
        $pricing->baseSave($request->input('document'));
        $pricing->save();
    }

}
