<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\PricingDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PricingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = PricingDocument::orderByDesc('created_at');
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
            ->through(function(PricingDocument $document) {
                return [
                    'id' => $document->id,
                    'date' => $document->htmlDate(),
                    'number' => $document->htmlNum(),
                    'completed' => $document->completed,
                    'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
                    'comment' => $document->comment,
                    'arrival' => !is_null($document->arrival_id) ? $document->arrival->htmlNumDate(): '-',

                    'url' => route('admin.accounting.pricing.show', $document),
                    'destroy' => route('admin.accounting.pricing.destroy', $document),
                ];
            });
    }
}
