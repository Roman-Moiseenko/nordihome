<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\MovementDocument;
use Illuminate\Http\Request;

class MovementRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = MovementDocument::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_begin'))) {
            $filters['date_begin'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_end'))) {
            $filters['date_end'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }
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
        if (($comment = $request->string('comment')) != '') {
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if (($staff_id = $request->integer('staff_id')) > 0) {
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

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
