<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\Distributor;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ArrivalRepository extends AccountingRepository
{
    private DistributorRepository $distributors;

    public function __construct(DistributorRepository $distributors)
    {
        $this->distributors = $distributors;
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = ArrivalDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(ArrivalDocument $document) => $this->ArrivalToArray($document));
    }

    public function ArrivalToArray(ArrivalDocument $document): array
    {
        return array_merge($document->toArray(), [
            'currency' => $document->currency->sign,
            'date' => $document->htmlDate(),
            'distributor_name' => $document->distributor->name,
            //'distributor_org' => $document->distributor->organization->short_name,
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'operation_text' => $document->operationText(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'supply' => $document->isSupply() ? 'Заказ № ' . $document->supply->number . ' от ' . $document->supply->htmlDate() : null,
        ]);
    }

    public function ArrivalWithToArray(ArrivalDocument $arrival): array
    {
        $withData = [
            'products' => $arrival->products()->with('product')->paginate(20)->toArray(),
            'distributor' => $this->distributors->DistributorForAccounting($arrival->distributor),
            'expense' => is_null($arrival->expense) ? null : array_merge($arrival->expense()->first()->toArray(),[
                'amount' => $arrival->expense->getAmount(),
            ]),
        ];

        return array_merge($this->ArrivalToArray($arrival), $withData);
    }

    public function getOperations(): array
    {

        return array_select(ArrivalDocument::OPERATIONS);
    }

    public function ExpenseWithToArray(ArrivalExpenseDocument $expense): array
    {
        return array_merge($expense->toArray(),[
            'currency_sign' => ($expense->currency) ? $expense->arrival->currency->sign : '₽',
            'items' => $expense->items()->get()->toArray(),
            'distributor' => $this->distributors->DistributorForAccounting($expense->arrival->distributor),
            'amount' => $expense->getAmount(),
            'arrival' => $expense->arrival()->first()->toArray(),
        ]);
    }
}
