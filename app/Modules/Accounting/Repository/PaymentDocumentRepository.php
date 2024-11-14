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

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ($request->integer('distributor') > 0) {
                $distributor= $request->integer('distributor');
                $filters['distributor'] = $distributor;
                $query->whereHas('recipient', function($query) use($distributor) {
                    $query->whereHas('distributor', function($query) use($distributor) {
                        $query->where('id', $distributor);
                    });
                });
            }
        },
        false);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(PaymentDocument $document) => $this->PaymentToArray($document));
    }

    public function PaymentToArray(PaymentDocument $payment): array
    {
        return array_merge($payment->toArray(), [
                'distributor_name' => $payment->recipient->distributor->name,
                //'distributor_org' => $payment->distributor->organization->short_name,
                'trader' => $payment->payer->full_name,
                'organization_id' => $payment->payer->organization_id,
                //'supply' => is_null($payment->supply_id) ? '' : $payment->supply->htmlNumDate(),
                'debit' => $payment->recipient->distributor->debit() - $payment->recipient->distributor->credit(),
                'currency' => $payment->recipient->distributor->currency->sign,
                'staff' => $payment->staff->fullname->getFullName(),
            ]
        );
    }

    public function PaymentWithToArray(PaymentDocument $payment): array
    {
        return array_merge($this->PaymentToArray($payment), [
            'distributor' => $this->distributors->DistributorForAccounting($payment->recipient->distributor),
            'decryptions' => $payment->decryptions()->with('supply')->get()->toArray(),
        ]);
    }
}
