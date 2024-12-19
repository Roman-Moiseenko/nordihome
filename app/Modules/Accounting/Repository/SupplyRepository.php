<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SupplyRepository extends AccountingRepository
{
    private DistributorRepository $distributors;

    public function __construct(DistributorRepository $distributors)
    {
        $this->distributors = $distributors;
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = SupplyDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(function (SupplyDocument $document) {
                return $this->SupplyToArray($document);
            });
    }

    public function getStacks(Request $request, &$filters): Arrayable
    {
        $query = SupplyStack::where('supply_id', null);
        $filters = [];
        if (($brand = $request->integer('brand')) > 0) {
            $filters['brand'] = $brand;
            $query->whereHas('product', function ($query) use ($brand) {
                $query->where('brand_id', $brand);
            });
        }
        if (($staff_id = $request->integer('staff_id')) > 0) {
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(function (SupplyStack $stack) {
                return [
                    'id' => $stack->id,
                    'name' => $stack->product->name,
                    'code' => $stack->product->code,
                    'quantity' => $stack->quantity,
                    'founded' => $stack->comment,
                    'order_id' => !is_null($stack->orderItem) ? $stack->orderItem->order_id : null,
                    'created_at' => $stack->created_at,
                    'brand' => $stack->product->brand->name,
                    'staff' => !is_null($stack->staff) ? $stack->staff->fullname->getFullName() : '-',
                ];
            });
    }

    public function SupplyToArray(SupplyDocument $document): array
    {
        return array_merge($document->toArray(), [
            'quantity' => $document->getQuantity(),
            'positions' => $document->products()->count(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'currency' => $document->distributor->currency->sign,

            'distributor_name' => $document->distributor->name,
            'date' => $document->htmlDate(),
            'status_pay' => ($document->getAmount() == 0) ? 0 : round($document->getPayment() / $document->getAmount(), 1),
            'status_out' => ($document->getQuantity() == 0) ? 0 : round($document->getOutQuantity() / $document->getQuantity(), 1)
        ]);
    }

    public function SupplyWithToArray(SupplyDocument $document, Request $request, &$filters): array
    {
        $query = $this->productFilters($document, $request, $filters);

        $withData = [
            'currency_exchange' => $document->distributor->currency->exchange,
            'products' => $query
                ->with('product')
                ->paginate($request->input('size', 20))
                ->toArray(),
            'distributor' => $this->distributors->DistributorForAccounting($document->distributor),
            'arrivals' => $document->arrivals()->get()->map(function (ArrivalDocument $document) {
                return array_merge($document->toArray(), [
                    'storage_name' => $document->storage->name,
                    'amount' => $document->getAmount(),
                    'quantity' => $document->getQuantity(),
                    'currency' => $document->currency->sign,
                ]);
            }),
        ];

        return array_merge(
            $this->commonItems($document),
            $this->SupplyToArray($document),
            $withData,
        );
    }
}
