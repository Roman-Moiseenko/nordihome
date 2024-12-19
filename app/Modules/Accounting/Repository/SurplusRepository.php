<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\SurplusDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SurplusRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = SurplusDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ($request->integer('storage') > 0) {
                $storage = $request->integer('storage');
                $filters['storage'] = $storage;
                $query->where('storage_id', $storage);
            }
        });

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(SurplusDocument $document) => $this->SurplusToArray($document));
    }

    private function SurplusToArray(SurplusDocument $document): array
    {
        return array_merge($document->toArray(), [
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
        ]);
    }

    public function SurplusWithToArray(SurplusDocument $document, Request $request, &$filters): array
    {
        $query = $this->productFilters($document, $request, $filters);
        return array_merge(
            $this->commonItems($document),
            $this->SurplusToArray($document),
            [
                'products' => $query
                    ->with('product')->paginate($request->input('size', 20))->toArray(),
                'inventory' => !is_null($document->inventory),
            ],
        );
    }
}
