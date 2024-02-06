<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Distributor;
use Illuminate\Http\Request;

class DistributorService
{

    public function create(Request $request)
    {
        $distributor = Distributor::register($request['name']);
        return $distributor;
    }

    public function update(Request $request, Distributor $distributor)
    {
        $distributor->name = $request['name'];
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
        foreach ($distributor->products as $product) {
            if ($product->id == $product_id) {
                $distributor->products()->updateExistingPivot($product_id, ['cost' => $cost]);
                return;
            }

        }
        $distributor->products()->attach($product_id, ['cost' => $cost]);
    }
}
