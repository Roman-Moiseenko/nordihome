<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\MovementDocument;
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
        });

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(MovementDocument $document) {
                return [
                    'id' => $document->id,
                    'date' => $document->htmlDate(),
                    'number' => $document->htmlNum(),
                    //'completed' => $document->completed,
                    'status' => $document->status,
                    'status_html' => $document->statusHTML(),
                    'storage_in' => $document->storageIn->name,
                    'storage_out' => $document->storageOut->name,
                    'quantity' => $document->getInfoData()['quantity'],
                    'comment' => $document->comment,
                    'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
                    'url' => route('admin.accounting.departure.show', $document),
                    'destroy' => route('admin.accounting.departure.destroy', $document),
                ];
            });
    }
}
