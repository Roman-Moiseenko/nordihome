<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\RefundDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class RefundRepository extends AccountingRepository
{
    private DistributorRepository $distributors;

    public function __construct(DistributorRepository $distributors)
    {
        $this->distributors = $distributors;
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = RefundDocument::withTrashed()->orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(RefundDocument $document) => $this->RefundToArray($document));
    }

    private function RefundToArray(RefundDocument $document): array
    {
        return array_merge($document->toArray(), [
            'trashed' => $document->trashed(),
            'currency' => $document->distributor->currency->sign,
            'exchange_fix' => $document->arrival->exchange_fix,
            'distributor_name' => $document->distributor->name,
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
        ]);
    }

    public function RefundWithToArray(RefundDocument $document, Request $request, &$filters): array
    {
        $query = $this->productFilters($document, $request, $filters);

        return array_merge(
            $this->commonItems($document),
            $this->RefundToArray($document),
            [
                'products' => $query
                    ->with('product')
                    ->paginate($request->input('size', 20))
                    ->toArray(),
                'distributor' => $this->distributors->DistributorForAccounting($document->distributor),
            ],
        );
    }
}
