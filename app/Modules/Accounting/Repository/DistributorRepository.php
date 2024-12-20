<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class DistributorRepository
{
    public function DistributorForAccounting(?Distributor $distributor): ?array
    {
        if (is_null($distributor)) return null;
        return [
            'name' => $distributor->name,
            'short_name' => $distributor->organization->short_name,
            'full_name' => $distributor->organization->full_name,
            'inn' => $distributor->organization->inn,
            'debit' => $distributor->debit(),
            'credit' => $distributor->credit(),
            'currency' => $distributor->currency->sign,
            'foreign' => $distributor->foreign,
            'organization_id' => $distributor->organization_id,
            'organizations' => $distributor->organizations,
        ];
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Distributor::orderBy('name');
        $filters = [];


        if (($name = $request->string('name')->trim()->value()) != '') {
            $filters['name'] = $name;
            $query->whereHas('organization', function ($query) use ($name) {
                $query->whereRaw("LOWER(short_name) like LOWER('%$name%')")
                    ->orWhereRaw("LOWER(full_name) like LOWER('%$name%')")
                    ->orWhere('inn', 'like', "%$name%")
                    ->orWhere('email', 'like', "%$name%")
                    ->orWhere('phone', 'like', "%$name%")
                    ->orWhereHas('contacts', function ($query) use ($name) {
                        $query->where('email', 'like', "%$name%")
                            ->where('phone', 'like', "%$name%");
                    });
            });
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Distributor $distributor) => $this->DistributorToArray($distributor));
    }

    private function DistributorToArray(Distributor $distributor): array
    {
        return array_merge($distributor->toArray(), [
            'organization' => $distributor->organization,
            'currency' => $distributor->currency,
            'debit' => $distributor->debit() - $distributor->credit(),
        ]);
    }

    public function DistributorWithToArray(Distributor $distributor, Request $request): array
    {
        return array_merge(

            $this->DistributorToArray($distributor),
            [
                'organizations' => $distributor->organizations,
                'contacts' => is_null($distributor->organization) ? [] : $distributor->organization->contacts,
                'supplies' => array_filter(array_map(
                    function (SupplyDocument $supply) {
                        if ($supply->getAmountRefunds() == $supply->getPayment()) return false;
                        return [
                            'id' => $supply->id,
                            'debt' => $supply->getAmountRefunds() - $supply->getPayment(),
                            'created_at' => $supply->created_at,
                            'number' => $supply->number,
                        ];
                    },
                    $distributor->supplies()->where('completed', true)->getModels()
                )),


                /*'products' => $distributor->products()
                    ->paginate($request->input('size', 20))
                    ->withQueryString()
                    ->through(fn(Product $product) => [
                        'name' => $product->name,
                        'code' => $product->code,
                        'quantity' => $product->getQuantity(),
                        'reserve' => $product->getQuantityReserve(),
                        'balance' =>$product->balance,
                        'price_retail' => $product->getPriceRetail(),
                        'cost' => $product->pivot->cost,
                        'pre_cost' => $product->pivot->pre_cost,
                    ]),*/
            ],
        );
    }

    public function ProductToArray(DistributorProduct $product): array
    {
        return [
            'id' => $product->product_id,
            'name' => $product->product->name,
            'code' => $product->product->code,
            'quantity' => $product->product->getQuantity(),
            'reserve' => $product->product->getQuantityReserve(),
            'balance' => $product->product->balance,
            'price_retail' => $product->product->getPriceRetail(),
            'cost' => $product->cost,
            'pre_cost' => $product->pre_cost,
        ];
    }

}
