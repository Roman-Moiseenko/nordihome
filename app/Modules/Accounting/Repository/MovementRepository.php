<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class MovementRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = MovementDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ( ($storage_out = $request->integer('storage_out'))> 0) {
                $filters['storage_out'] = $storage_out;
                $query->where('storage_out', $storage_out);
            }
            if (($storage_in = $request->integer('storage_in')) > 0) {
                $filters['storage_in'] = $storage_in;
                $query->where('storage_in', $storage_in);
            }
            if (($status = $request->integer('status')) > 0) {
                $filters['status'] = $status;
                $query->where('status', $status);
            }
        }, false);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(MovementDocument $document) => $this->MovementToArray($document));
    }

    public function MovementToArray(MovementDocument $document): array
    {
        return array_merge($document->toArray(), [
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'status_html' => $document->statusHTML(),
            'storage_in' => $document->storageIn->toArray(),
            'storage_out' => $document->storageOut->toArray(),
            'quantity' => $document->getQuantity(),
            'arrival_text' => !is_null($document->arrival) ? ($document->arrival->number . ' от ' . $document->arrival->htmlDate()) : '',
        ]);
    }
    public function MovementWithToArray(MovementDocument $document): array
    {
        return array_merge($this->MovementToArray($document), [
            'is_active' => $document->isFinished(),
            'is_departure' => $document->isDeparture(),
            'is_arrival' => $document->isArrival(),
            'products' => $document->products()
                ->with('product')
                ->paginate(20)
                ->withQueryString()
                ->through(fn(MovementProduct $movementProduct) => array_merge($movementProduct->toArray(), [
                    'quantity_out' => $document->storageOut->getAvailable($movementProduct->product),
                    'quantity_in' => $document->storageIn->getAvailable($movementProduct->product),
                ])),
        ]);
    }
}
