<?php

namespace App\Modules\Accounting\Repository;

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
                $distributor = $request->integer('distributor');
                $filters['distributor'] = $distributor;
                $query->whereHas('recipient', function ($query) use ($distributor) {
                    $query->whereHas('distributor', function ($query) use ($distributor) {
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
        $array = $payment->toArray();
        return array_merge($array, [
                'distributor_name' => $payment->recipient->distributor->name,
                'trader' => $payment->payer->full_name,
                'organization_id' => $payment->payer->id,
                'debit' => $payment->recipient->distributor->debit() - $payment->recipient->distributor->credit(),
                'currency' => $payment->recipient->distributor->currency->sign,
                'staff' => $payment->staff->fullname->getFullName(),
            ]
        );
    }

    public function PaymentWithToArray(PaymentDocument $document): array
    {
        return array_merge(
            $this->commonItems($document),
            $this->PaymentToArray($document),
            [
                'distributor' => $this->distributors->DistributorForAccounting($document->recipient->distributor),
                'decryptions' => $document->decryptions()->with('supply')->get()->toArray(),
            ],
        );
    }
}
