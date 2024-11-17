<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\InventoryDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class InventoryRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = InventoryDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if (($storage_id = $request->integer('$storage_id')) > 0) {
                $filters['storage_id'] = $storage_id;
                $query->where('storage_id', $storage_id);
            }
        }, false);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(InventoryDocument $document) => $this->InventoryToArray($document));
    }

    public function InventoryToArray(InventoryDocument $document): array
    {
        return array_merge($document->toArray(), [
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'storage' => $document->storage->toArray(),
        ]);

    }

    public function InventoryWithToArray(InventoryDocument $document): array
    {
        return array_merge(
            $this->commonItems($document),
            $this->InventoryToArray($document),
            [
                'products' => $document->products()->with('product')->paginate(20),
                'amount_formal' => $document->getFormalAmount(),
                'amount_actually' => $document->getActuallyAmount(),
            ],
        );
    }
}
