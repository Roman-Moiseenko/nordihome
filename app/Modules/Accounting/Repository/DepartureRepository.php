<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\DepartureDocument;
use Illuminate\Http\Request;

class DepartureRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = DepartureDocument::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_begin'))) {
            $filters['date_begin'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_end'))) {
            $filters['date_end'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }

        if ($request->has('draft')) {
            $draft = $request->string('draft');
            $filters['draft'] = $draft;
            $query->where('completed', false);
        }

        if ($request->integer('storage') > 0) {
            $storage= $request->integer('storage');
            $filters['storage'] = $storage;
            $query->where('storage_id', $storage);
        }
        if ($request->string('comment') != '') {
            $comment = $request->string('comment')->trim()->value();
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if ($request->integer('staff_id') > 0) {
            $staff_id = $request->integer('staff_id');
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(DepartureDocument $document) {
                return [
                    'id' => $document->id,
                    'date' => $document->htmlDate(),
                    'number' => $document->htmlNum(),
                    'completed' => $document->completed,
                    'storage' => $document->storage->name,
                    'comment' => $document->comment,
                    'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
                    'url' => route('admin.accounting.departure.show', $document),
                    'destroy' => route('admin.accounting.departure.destroy', $document),
                ];
            });
    }
}
