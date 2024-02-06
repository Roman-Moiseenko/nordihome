<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\ArrivalHasCompleted;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class ArrivalService
{
    private StorageService $storages;
    private DistributorService $distributors;

    public function __construct(StorageService $storages, DistributorService $distributors)
    {
        $this->storages = $storages;
        $this->distributors = $distributors;
    }

    public function create(Request $request): ArrivalDocument
    {
        $currency = Currency::find((int)$request['currency']);
        $arrival = ArrivalDocument::register(
            $request['number'] ?? '',
            (int)$request['distributor'],
            (int)$request['storage'],
            $currency
        );
        return $arrival;
    }

    public function update(Request $request, ArrivalDocument $arrival)
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $arrival->number = $request['number'] ?? '';
        $arrival->distributor_id = (int)$request['distributor'];
        $arrival->storage_id = (int)$request['storage'];
        $arrival->save();
        $currency = Currency::find((int)$request['currency']);

        if ($currency->id != $arrival->currency_id) {
            $arrival->currency_id = $currency->id;
            $arrival->setExchange($currency->exchange);
        } elseif ($currency->exchange != $arrival->exchange_fix) {
            $arrival->setExchange($currency->exchange);
        }
        return $arrival;
    }

    public function destroy(ArrivalDocument $arrival)
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $arrival->delete();
    }


    public function add(Request $request, ArrivalDocument $arrival)
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($request['product_id']);
        $distributor_cost = $arrival->distributor->getCostItem($product->id); //Ищем у поставщика товар, если есть, берем закупочную цену
        $product_sell = $product->lastPrice->value ?? 0;
        //Добавляем в документ
        $arrival->arrivalProducts()->create([
            'product_id' => $product->id,
            'quantity' => (int)$request['quantity'],
            'cost_currency' => $distributor_cost,
            'cost_ru' => $arrival->exchange_fix * $distributor_cost,
            'price_sell' => $product_sell,
            ]);
        $arrival->refresh();
    }

    //Для AJAX
    public function set(Request $request, ArrivalProduct $item): float|int
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        //Меняем данные
        $item->quantity = (int)$request['quantity'];
        $item->cost_currency = (float)$request['cost'];
        $item->cost_ru = ceil($item->document->exchange_fix * $item->cost_currency * 100) / 100;
        $item->price_sell = (int)$request['price'];
        $item->save();
        return $item->cost_ru;
    }

    public function getInfoData(ArrivalDocument $arrival)
    {
        $result = [
            'cost_currency' => 0,
            'quantity' => 0,
            'cost_ru' => 0,
        ];

        foreach ($arrival->arrivalProducts as $item) {
            $result['quantity'] += $item->quantity;
            $result['cost_currency'] += $item->quantity * $item->cost_currency;
        }
        $result['cost_ru'] = ceil($result['cost_currency'] * $arrival->exchange_fix * 100) / 100;

        return $result;
    }

    public function completed(ArrivalDocument $arrival)
    {
        //Проведение документа
        $storage = $arrival->storage;
        $distributor = $arrival->distributor;
        //Проходим все товары и добавляем Поставщику с новой ценой, если она изменилась или товара нет

        $this->storages->arrival($arrival->storage, $arrival->arrivalProducts()->getModels());

        foreach ($arrival->arrivalProducts as $item) {
            $this->distributors->arrival($arrival->distributor, $item->product_id, $item->cost_currency);
            $item->product->setPrice($item->price_sell);
        }

        $arrival->completed();
        event(new ArrivalHasCompleted($arrival));
        //Уведомление .... Если ктото подписан на товар -- Событие event(new ArrivalHasCompleted($document))
    }

}
