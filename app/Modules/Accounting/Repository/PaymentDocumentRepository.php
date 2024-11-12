<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\PaymentDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PaymentDocumentRepository extends AccountingRepository
{
    private DistributorRepository $distributors;

    public function __construct(DistributorRepository $distributors)
    {
        $this->distributors = $distributors;
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = PaymentDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(PaymentDocument $document) => $this->PaymentToArray($document));
    }

    public function PaymentToArray(PaymentDocument $payment): array
    {
        return array_merge($payment->toArray(),
            [
                'distributor_name' => $payment->distributor->name,
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
            'distributor' => $this->distributors->DistributorForAccounting($payment->distributor),
        ], $this->PaymentToArray($payment));
    }
}
