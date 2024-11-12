<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyStack;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SupplyRepository extends AccountingRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = SupplyDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(function(SupplyDocument $document) {
                return $this->SupplyToArray($document);
            });
    }

    public function getStacks(Request $request, &$filters): Arrayable
    {
        $query = SupplyStack::where('supply_id', null);
        $filters = [];
        if (($brand = $request->integer('brand')) > 0) {
            $filters['brand'] = $brand;
            $query->whereHas('product', function ($query) use($brand) {
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
            ->through(function(SupplyStack $stack) {
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
        return  array_merge($document->toArray(),[
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'currency' => $document->distributor->currency->sign,
            'distributor' => $document->distributor->name,
            'distributor_org' => $document->distributor->organization->short_name,
            'date' => $document->htmlDate(),
        ]);
    }

    public function SupplyWithToArray(SupplyDocument $document): array
    {
        $withData = [
            'products' => $document->products()->with('product')->paginate(20)->toArray(),
        ];

        return array_merge($this->SupplyToArray($document), $withData);
    }
}
