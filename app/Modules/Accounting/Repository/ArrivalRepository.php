<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\ArrivalDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class ArrivalRepository extends AccountingRepository
{

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
            'distributor' => $document->distributor->name,
            'distributor_org' => $document->distributor->organization->short_name,
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
        ];

        return array_merge($this->ArrivalToArray($arrival), $withData);
    }

    public function getOperations(): array
    {

        return array_select(ArrivalDocument::OPERATIONS);
    }
}
