<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\RefundDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class RefundRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = RefundDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(RefundDocument $document) => $this->RefundToArray($document));
    }

    private function RefundToArray(RefundDocument $document): array
    {
        return array_merge($document->toArray(), [
            'currency' => $document->distributor->currency->sign,
            'distributor' => $document->distributor->name,
            'distributor_org' => $document->distributor->organization->short_name,
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
        ]);
    }

    public function RefundWithToArray(RefundDocument $document): array
    {
        return array_merge($this->RefundToArray($document), [
            'products' => $document->products()->with('product')->paginate(20)->toArray(),

        ]);
    }
}
