<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\AccountingDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

abstract class AccountingRepository
{
    abstract public function getIndex(Request $request, &$filters): Arrayable;

    final protected function filters(&$query, &$filters, $request, callable $func = null, bool $has_distributor = true): void
    {
        $filters = [];
        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }

        if ($request->has('draft')) {
            $draft = $request->string('draft');
            $filters['draft'] = $draft;
            $query->where('completed', false);
        }

        if ($has_distributor && $request->integer('distributor') > 0) {
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

        if (!is_null($func)) $func($query, $filters, $request); ///, function (&$query, &$filters, $request) {}
        if (count($filters) > 0) $filters['count'] = count($filters);
    }

    protected function commonItems(AccountingDocument $document): array
    {
        return [
            'based' => $document->onBased(),
            'founded' => $document->onFounded(),
            'document_name' => $document->documentName(),
        ];
    }
}
