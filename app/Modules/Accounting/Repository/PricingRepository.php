<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PricingRepository extends AccountingRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = PricingDocument::orderByDesc('created_at');

        $this->filters($query, $filters, $request, function (&$query, &$filters, $request) {
            if ($request->integer('distributor') > 0) {
                $distributor= $request->integer('distributor');
                $filters['distributor'] = $distributor;
                $query->whereHas('arrival', function($query) use($distributor) {
                    $query->whereHas('distributor', function($query) use($distributor) {
                        $query->where('id', $distributor);
                    });
                });
            }
        }, false);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(PricingDocument $document) => $this->PricingToArray($document));
    }

    public function PricingToArray(PricingDocument $document): array
    {
        return array_merge($document->toArray(), [
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            'arrival' => is_null($document->arrival_id) ? null : $document->arrival()->first()->toArray(),
        ]);
    }

    public function PricingWithToArray(PricingDocument $document, Request $request): array
    {

        return array_merge($this->PricingToArray($document), [
            'products' => $document->products()->paginate($request->input('size', 20))
                ->withQueryString()
                ->through(fn(PricingProduct $product) => array_merge($product->toArray(), [
                    'name' => $product->product->name,
                    'code' => $product->product->code,
                    'price_cost_old' => $product->product->getPriceCost($document->isCompleted()),
                    'price_retail_old' => $product->product->getPriceRetail($document->isCompleted()),
                    'price_bulk_old' => $product->product->getPriceBulk($document->isCompleted()),
                    'price_special_old' => $product->product->getPriceSpecial($document->isCompleted()),
                    'price_min_old' => $product->product->getPriceMin($document->isCompleted()),
                    'price_pre_old' => $product->product->getPricePre($document->isCompleted()),
                ])),
        ]);
    }
}
