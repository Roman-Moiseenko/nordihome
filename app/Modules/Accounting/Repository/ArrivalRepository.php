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
            'distributor_name' => is_null($document->distributor_id) ? '<Инвентаризация>' : $document->distributor->name,
            'quantity' => $document->getQuantity(),
            'amount' => $document->getAmount(),
            'operation_text' => $document->operationText(),
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'supply' => $document->isSupply() ? 'Заказ № ' . $document->supply->number . ' от ' . $document->supply->htmlDate() : null,
        ]);
    }

    public function ArrivalWithToArray(ArrivalDocument $document, Request $request): array
    {
        $withData = [
            'products' => $document->products()->with('product')->paginate($request->input('size', 20))->toArray(),
            'distributor' => $this->distributors->DistributorForAccounting($document->distributor),
            'expense' => is_null($document->expense) ? null : array_merge($document->expense()->first()->toArray(),[
                'amount' => $document->expense->getAmount(),
            ]),
        ];

        return array_merge(
            $this->commonItems($document),
            $this->ArrivalToArray($document),
            $withData,
        );
    }

    public function getOperations(): array
    {
        return array_select(ArrivalDocument::OPERATIONS);
    }

    public function ExpenseWithToArray(ArrivalExpenseDocument $document): array
    {
        return array_merge(
            $this->commonItems($document),
            $document->toArray(),
            [
            'currency_sign' => ($document->currency) ? $document->arrival->currency->sign : '₽',
            'items' => $document->items()->get()->toArray(),
            'distributor' => $this->distributors->DistributorForAccounting($document->arrival->distributor),
            'amount' => $document->getAmount(),
            'arrival' => $document->arrival()->first()->toArray(),

        ]);
    }
}
