<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\ArrivalDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ArrivalRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = ArrivalDocument::orderByDesc('created_at');
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

        if ($request->integer('distributor') > 0) {
            $distributor= $request->integer('distributor');
            $filters['distributor'] = $distributor;
            $query->where('distributor_id', $distributor);
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
            ->through(fn(ArrivalDocument $document) => $this->ArrivalToArray($document));
    }

    public function ArrivalToArray(ArrivalDocument $document): array
    {
        //$infoData = $document->getInfoData();
        return array_merge($document->toArray(), [
            'date' => $document->htmlDate(),
            'number' => $document->htmlNum(),
            'distributor' => $document->distributor->name,
            'quantity' => 0, //$infoData['quantity'],
            'amount' => 0, //$infoData['cost_currency'] . ' ' . $infoData['currency_sign'],
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
        ]);
    }
}
