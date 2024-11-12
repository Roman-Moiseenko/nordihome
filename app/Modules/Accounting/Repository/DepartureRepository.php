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
                $storage= $request->integer('storage');
                $filters['storage'] = $storage;
                $query->where('storage_id', $storage);
            }
        });

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
