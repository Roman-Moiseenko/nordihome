<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\DepartureDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class DepartureRepository extends AccountingRepository
{
//TODO
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = DepartureDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ($request->integer('storage') > 0) {
                $storage = $request->integer('storage');
                $filters['storage'] = $storage;
                $query->where('storage_id', $storage);
            }
        });

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(DepartureDocument $document) => $this->DepartureToArray($document));
    }

    public function DepartureToArray(DepartureDocument $document): array
    {
        return array_merge($document->toArray(), [
            'date' => $document->htmlDate(),
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
        ]);
    }

    public function DepartureWithToArray(DepartureDocument $document): array
    {
        return array_merge(
            $this->commonItems($document),
            $this->DepartureToArray($document),
            [
                'products' => $document->products()->with('product')->paginate(20)->toArray(),
            ],
        );
    }
}
