<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\PaymentDocument;
use Illuminate\Http\Request;

class PaymentDocumentRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = PaymentDocument::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }
        if (($request->has('draft')) > 0) {
            $filters['draft'] = true;
            $query->where('completed', false);
        }
        if (($distributor = $request->integer('distributor')) > 0) {
            $filters['distributor'] = $distributor;
            $query->where('distributor_id', $distributor);
        }
        if (($comment = $request->string('comment')->trim()->value()) != '') {
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if (($staff_id = $request->integer('staff_id')) > 0) {
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(PaymentDocument $document) => $this->PaymentToArray($document));
    }

    public function PaymentToArray(PaymentDocument $payment): array
    {
        return array_merge($payment->toArray(),
            [
                'distributor' => $payment->distributor->name,
                'distributor_org' => $payment->distributor->organization->short_name,
                'trader' => $payment->trader->organization->full_name,
                'organization_id' => $payment->trader->organization_id,
                'supply' => is_null($payment->supply_id) ? '' : $payment->supply->htmlNumDate(),
                'debit' => $payment->distributor->debit() - $payment->distributor->credit(),
                'currency' => $payment->distributor->currency->sign,
                'staff' => $payment->staff->fullname->getFullName(),
            ]
        );
    }

    public function PaymentWithToArray(PaymentDocument $payment): array
    {
        return array_merge([

        ], $this->PaymentToArray($payment));
    }
}
