<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class DistributorService
{

    public function create(Request $request): Distributor
    {
        $distributor = Distributor::register($request['name'], (int)$request['currency_id']);

        return $distributor;
    }

    public function update(Request $request, Distributor $distributor): Distributor
    {
        $distributor->name = $request['name'];
        $distributor->currency_id = (int)$request['currency_id'];
        $distributor->save();
        return $distributor;
    }

    public function destroy(Distributor $distributor)
    {
        if (!empty($distributor->arrivals)) throw new \DomainException('Имеются документы, удалить нельзя');
        $distributor->delete();
    }

    public function arrival(Distributor $distributor, int $product_id, float $cost)
    {
        //Поступление товара, списком
        /** @var Product $_product */
        $_product = Product::find($product_id);
        foreach ($distributor->products as $product) {
            if ($product->id == $_product->id) {
                $distributor->updateProduct($product, $cost);
                return;
            }
        }
        $distributor->addProduct($_product, $cost);
    }
}
