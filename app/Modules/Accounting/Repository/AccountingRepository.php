<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\AccountingDocument;
use App\Modules\Base\Traits\FiltersRepository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

abstract class AccountingRepository
{
    use FiltersRepository;

    abstract public function getIndex(Request $request, &$filters): Arrayable;

    final protected function filters(&$query, &$filters, $request, callable $func = null, bool $has_distributor = true): void
    {
        $filters = [];

        $this->_date_from($request, $filters, $query);
        $this->_date_to($request, $filters, $query);
        $this->_comment($request, $filters, $query);
        $this->_staff_id($request, $filters, $query);

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

    protected function productFilters(AccountingDocument $document, Request $request, &$filters): HasMany
    {
        $query = $document->products();
        if ($request->has('product')) {
            $product = $request->string('product')->trim()->value();
            $filters['product'] = $product;
            $query->whereHas('product', function ($query) use ($product) {
                $query->whereRaw("LOWER(name) LIKE LOWER('%$product%')")
                    ->orWhere('code', 'like', "%$product%")
                    ->orWhere('code_search', 'like', "%$product%");
            });
        } else {
            $filters = [];
        }
        return $query;
    }
}
