<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
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
        $query = ArrivalDocument::withTrashed()->orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(ArrivalDocument $document) => $this->ArrivalToArray($document));
    }

    public function ArrivalToArray(ArrivalDocument $document): array
    {
        return array_merge($document->toArray(), [
            'trashed' => $document->trashed(),
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

    public function ArrivalWithToArray(ArrivalDocument $document, Request $request, &$filters): array
    {
        $query = $this->productFilters($document, $request, $filters);
        $withData = [
            'products' => $query
                ->with('product')
                ->paginate($request->input('size', 20))
                ->withQueryString()
                ->through(function (ArrivalProduct $arrivalProduct) use ($document) {
                    $pre_cost = null;
                    if (!is_null($document->distributor)
                        && !is_null($product = $document->distributor->getProduct($arrivalProduct->product_id)))
                        $pre_cost = $product->pivot->pre_cost;


                    return array_merge($arrivalProduct->toArray(), [
                        'pre_cost' => $pre_cost,
                        'measuring' => $arrivalProduct->product->measuring->name,
                    ]);
                }
                ),
            'distributor' => $this->distributors->DistributorForAccounting($document->distributor),
            'expense_amount' => $document->getExpenseAmount(),
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
                'trashed' => $document->trashed(),
                'currency_sign' => ($document->currency) ? $document->arrival->currency->sign : '₽',
                'items' => $document->items()->get()->toArray(),
                'distributor' => $this->distributors->DistributorForAccounting($document->arrival->distributor),
                'amount' => $document->getAmount(),
                'arrival' => $document->arrival()->first()->toArray(),

            ]);
    }
}
