<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\PricingDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PricingRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = PricingDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(PricingDocument $document) {
                return [
                    'id' => $document->id,
                    'date' => $document->htmlDate(),
                    'number' => $document->htmlNum(),
                    'completed' => $document->completed,
                    'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
                    'comment' => $document->comment,
                    'arrival' => !is_null($document->arrival_id) ? $document->arrival->htmlNumDate(): '-',
/*
                    'url' => route('admin.accounting.pricing.show', $document),
                    'destroy' => route('admin.accounting.pricing.destroy', $document),
                    'copy' => route('admin.accounting.pricing.copy', $document),*/
                ];
            });
    }
}
